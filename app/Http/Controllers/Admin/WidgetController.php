<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use App\Services\FieldTypeService;
use App\Services\WidgetPermissionService;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index()
    {
        // Get user from request attributes (project context) or auth (web context)
        $user = request()->attributes->get('auth_user') ?? auth()->user();
        
        // Debug log
        \Log::info('Widget index - auth debug', [
            'auth_user_from_request' => request()->attributes->get('auth_user') ? 'YES' : 'NO',
            'auth_user_from_auth' => auth()->user() ? 'YES' : 'NO',
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'user_level' => $user?->level,
            'session_project_user_id' => session('project_user_id'),
            'env' => config('app.env'),
        ]);
        
        // Skip permission check entirely in local environment
        if (config('app.env') !== 'local') {
            $permissionService = new WidgetPermissionService();
            
            // Check if user can manage widgets
            if (!$permissionService->canManageWidgets($user)) {
                \Log::error('Widget permission denied', [
                    'user' => $user ? $user->toArray() : null,
                    'can_manage' => $permissionService->canManageWidgets($user),
                ]);
                abort(403, 'You do not have permission to manage widgets');
            }
        }

        $existingWidgets = Widget::orderBy('area')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('area')
            ->map(fn ($widgets) => $widgets->map(fn ($w) => [
                'type' => $w->type,
                'name' => $w->name,
                'area' => $w->area,
                'sort_order' => $w->sort_order,
                'is_active' => $w->is_active,
                'settings' => $w->settings,
                'variant' => $w->variant ?? 'default',
            ]));

        // Get only accessible widgets for current user
        $permissionService = new WidgetPermissionService();
        $availableWidgets = $permissionService->getAccessibleWidgetsByCategory($user);
        
        // Debug: dd available widgets
        dd([
            'availableWidgets' => $availableWidgets,
            'count' => count($availableWidgets),
            'user' => $user ? ['id' => $user->id, 'role' => $user->role] : null,
        ]);

        // Check if we're in project context
        $projectCode = request()->route('projectCode');
        $currentProject = null;
        
        if ($projectCode) {
            $currentProject = (object) ['code' => $projectCode];
        }

        $permissionSummary = config('app.env') === 'local' ? 
            ['can_manage_widgets' => true, 'can_toggle_widgets' => true, 'accessible_widget_count' => 999, 'total_widget_count' => 999, 'is_super_admin' => true] :
            $permissionService->getPermissionSummary();

        return view('cms.widgets.builder', compact('existingWidgets', 'availableWidgets', 'currentProject', 'permissionSummary'));
    }

    public function create()
    {
        return view('cms.widgets.create');
    }

    public function store(Request $request)
    {
        $permissionService = new WidgetPermissionService();
        
        // Check if user can manage widgets
        if (!$permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create widgets'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable',
            'config' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'variant' => 'nullable|string',
        ]);

        // Validate widget type exists in registry or custom templates
        if (!WidgetRegistry::exists($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$validated['type']}' not found"
            ], 422);
        }

        // Check if user can access this widget type
        if (!$permissionService->canAccessWidget($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "You do not have permission to use '{$validated['type']}' widget"
            ], 403);
        }

        // Check if widget is enabled
        if (!$permissionService->isWidgetEnabled($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$validated['type']}' is currently disabled"
            ], 422);
        }

        // Validate dependencies
        $dependencyErrors = $permissionService->validateDependencies($validated['type']);
        if (!empty($dependencyErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Widget dependencies not met: ' . implode(', ', $dependencyErrors)
            ], 422);
        }

        // Ensure settings is array
        if (\is_string($validated['settings'] ?? null)) {
            $validated['settings'] = json_decode($validated['settings'], true);
        }

        // Nếu có config, merge vào settings
        if (! empty($validated['config'])) {
            $validated['settings'] = [...($validated['settings'] ?? []), 'config' => $validated['config']];
        }
        unset($validated['config']);

        Widget::create($validated);
        clear_widget_cache($validated['area']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Widget saved successfully']);
        }

        return redirect()->back()->with('success', 'Widget created successfully');
    }

    public function edit(Widget $widget)
    {
        return view('cms.widgets.edit', compact('widget'));
    }

    public function update(Request $request, Widget $widget)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable|json',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $widget->update($validated);
        clear_widget_cache($widget->area);

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.widgets.index', $projectCode)
            : route('cms.widgets.index');

        return redirect($route)->with('success', 'Widget updated successfully');
    }

    public function destroy(Widget $widget)
    {
        $area = $widget->area;
        $widget->delete();
        clear_widget_cache($area);

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.widgets.index', $projectCode)
            : route('cms.widgets.index');

        return redirect($route)->with('success', 'Widget deleted successfully');
    }

    /**
     * Save multiple widgets at once
     */
    public function saveWidgets(Request $request)
    {
        try {
            $widgets = $request->input('widgets', []);
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Get tenant_id from session
            $currentProject = session('current_project');
            $tenantId = null;
            if (\is_array($currentProject)) {
                $tenantId = $currentProject['id'] ?? null;
            } elseif (\is_object($currentProject)) {
                $tenantId = $currentProject->id ?? null;
            }

            // Clear existing widgets for the areas being updated (only for this tenant)
            $areas = collect($widgets)->pluck('area')->unique();
            foreach ($areas as $area) {
                $query = Widget::where('area', $area);
                if ($tenantId) {
                    $query->where('tenant_id', $tenantId);
                }
                $query->delete();
            }

            // Save new widgets
            foreach ($widgets as $widgetData) {
                try {
                    $validated = $this->validateWidgetData($widgetData);
                    // Add tenant_id
                    if ($tenantId) {
                        $validated['tenant_id'] = $tenantId;
                    }
                    Widget::create($validated);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Widget '{$widgetData['name']}': " . $e->getMessage();
                }
            }

            // Clear cache for all affected areas
            foreach ($areas as $area) {
                clear_widget_cache($area);
            }

            if ($errorCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully saved {$successCount} widgets",
                    'saved' => $successCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Saved {$successCount} widgets, {$errorCount} failed",
                    'saved' => $successCount,
                    'failed' => $errorCount,
                    'errors' => $errors
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving widgets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear widgets in a specific area
     */
    public function clearArea(Request $request)
    {
        $area = $request->input('area', 'homepage-main');
        
        // Get tenant_id from session
        $currentProject = session('current_project');
        $tenantId = null;
        if (\is_array($currentProject)) {
            $tenantId = $currentProject['id'] ?? null;
        } elseif (\is_object($currentProject)) {
            $tenantId = $currentProject->id ?? null;
        }
        
        $query = Widget::where('area', $area);
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        $count = $query->count();
        $query->delete();
        clear_widget_cache($area);

        return response()->json([
            'success' => true,
            'message' => "Cleared {$count} widgets from {$area}"
        ]);
    }

    /**
     * Clear widget cache
     */
    public function clearCache()
    {
        clear_widget_cache();
        WidgetRegistry::clearCache(); // Clear discovery cache
        return response()->json(['success' => true, 'message' => 'Widget cache cleared']);
    }

    /**
     * Validate widget data
     */
    private function validateWidgetData($data)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'variant' => 'nullable|string'
        ];

        $validator = \Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        $validated = $validator->validated();

        // Validate widget type exists
        if (!WidgetRegistry::exists($validated['type'])) {
            throw new \Exception("Widget type '{$validated['type']}' not found in registry");
        }

        // Process settings
        if (isset($validated['settings']) && is_string($validated['settings'])) {
            $validated['settings'] = json_decode($validated['settings'], true);
        }

        // Validate settings against widget metadata (only if settings are provided)
        if (!empty($validated['settings'])) {
            try {
                $widgetClass = WidgetRegistry::get($validated['type']);
                if ($widgetClass) {
                    $tempWidget = new $widgetClass($validated['settings'] ?? [], $validated['variant'] ?? 'default');
                    $tempWidget->validateSettings();
                }
            } catch (\Exception $e) {
                // Log warning but don't fail - allow saving with potentially invalid settings
                \Log::warning("Widget settings validation warning for {$validated['type']}: " . $e->getMessage());
                // Only throw if it's a critical error, not just validation
                if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'class')) {
                    throw new \Exception('Widget settings validation failed: ' . $e->getMessage());
                }
            }
        }

        // Set defaults
        $validated['is_active'] ??= true;
        $validated['sort_order'] ??= 0;
        $validated['variant'] ??= 'default';

        return $validated;
    }

    /**
     * Get widget preview
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable|array',
            'variant' => 'nullable|string'
        ]);

        try {
            $preview = WidgetRegistry::getPreview(
                $validated['type'],
                $validated['settings'] ?? [],
                $validated['variant'] ?? 'default'
            );

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview generation failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Discover widgets
     */
    public function discover()
    {
        try {
            $discovered = WidgetRegistry::discover();
            $conflicts = WidgetRegistry::validateNamespaces();

            return response()->json([
                'success' => true,
                'discovered' => count($discovered),
                'widgets' => $discovered,
                'conflicts' => $conflicts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Widget discovery failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widget form fields
     */
    public function getFields(Request $request)
    {
        $type = $request->input('type') ?? $request->get('type');
        $settings = $request->input('settings') ?? $request->get('settings', []);
        
        // Parse settings if it's a JSON string
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }
        
        if (!$type || !WidgetRegistry::exists($type)) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$type}' not found"
            ], 404);
        }

        try {
            $config = WidgetRegistry::getConfig($type);
            $fields = $config['fields'] ?? [];
            
            $fieldTypeService = new FieldTypeService();
            $formHtml = $fieldTypeService->renderForm($fields, $settings);

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'form_html' => $formHtml,
                'variants' => $config['variants'] ?? ['default' => 'Default']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get widget fields: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle widget enabled/disabled state
     */
    public function toggleWidget(Request $request)
    {
        $type = $request->get('type');
        $enabled = $request->boolean('enabled');
        
        $permissionService = new WidgetPermissionService();
        
        if (!$permissionService->canToggleWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to toggle widgets'
            ], 403);
        }

        if (!WidgetRegistry::exists($type)) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$type}' not found"
            ], 404);
        }

        try {
            if ($enabled) {
                $result = $permissionService->enableWidget($type);
                $message = "Widget '{$type}' enabled successfully";
            } else {
                $result = $permissionService->disableWidget($type);
                $message = "Widget '{$type}' disabled successfully";
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to toggle widget state'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling widget: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widget permissions for current user
     */
    public function getPermissions()
    {
        $permissionService = new WidgetPermissionService();
        
        return response()->json([
            'success' => true,
            'permissions' => $permissionService->getPermissionSummary()
        ]);
    }

    /**
     * Export widgets configuration
     */
    public function export(Request $request)
    {
        $permissionService = new WidgetPermissionService();
        
        if (!$permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to export widgets'
            ], 403);
        }

        try {
            $importExportService = new \App\Services\WidgetImportExportService();
            
            $options = [
                'areas' => $request->get('areas'),
                'types' => $request->get('types'),
                'include_metadata' => $request->boolean('include_metadata', true)
            ];
            
            $exportData = $importExportService->exportWidgets($options);
            
            return response()->json([
                'success' => true,
                'data' => $exportData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import widgets configuration
     */
    public function import(Request $request)
    {
        $permissionService = new WidgetPermissionService();
        
        if (!$permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to import widgets'
            ], 403);
        }

        $validated = $request->validate([
            'import_data' => 'required|array',
            'overwrite_existing' => 'boolean',
            'validate_only' => 'boolean'
        ]);

        try {
            $importExportService = new \App\Services\WidgetImportExportService();
            
            $options = [
                'overwrite_existing' => $validated['overwrite_existing'] ?? false,
                'validate_only' => $validated['validate_only'] ?? false
            ];
            
            $result = $importExportService->importWidgets($validated['import_data'], $options);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create widget backup
     */
    public function createBackup(Request $request)
    {
        $permissionService = new WidgetPermissionService();
        
        if (!$permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create backups'
            ], 403);
        }

        try {
            $importExportService = new \App\Services\WidgetImportExportService();
            $backupName = $request->get('name');
            
            $backupPath = $importExportService->createBackup($backupName);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_path' => basename($backupPath)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available backups
     */
    public function getBackups()
    {
        $permissionService = new WidgetPermissionService();
        
        if (!$permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view backups'
            ], 403);
        }

        try {
            $importExportService = new \App\Services\WidgetImportExportService();
            $backups = $importExportService->getAvailableBackups();
            
            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get backups: ' . $e->getMessage()
            ], 500);
        }
    }
}
