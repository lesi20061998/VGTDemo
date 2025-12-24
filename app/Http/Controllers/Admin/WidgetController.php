<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index()
    {
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
            ]));

        $availableWidgets = WidgetRegistry::getByCategory();

        // Check if we're in project context
        $projectCode = request()->route('projectCode');
        $currentProject = null;
        
        if ($projectCode) {
            $currentProject = (object) ['code' => $projectCode];
        }

        return view('cms.widgets.builder', compact('existingWidgets', 'availableWidgets', 'currentProject'));
    }

    public function create()
    {
        return view('cms.widgets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable',
            'config' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        // Xử lý settings
        if (is_string($validated['settings'] ?? null)) {
            $validated['settings'] = json_decode($validated['settings'], true);
        }

        // Nếu có config, merge vào settings
        if (! empty($validated['config'])) {
            $validated['settings'] = array_merge($validated['settings'] ?? [], ['config' => $validated['config']]);
        }
        unset($validated['config']);

        // Ensure tenant_id is set for project context
        $projectCode = request()->route('projectCode');
        if ($projectCode && ! isset($validated['tenant_id'])) {
            // You might need to get the tenant_id from the project
            // For now, let's use the session or a default approach
            if (session('current_tenant_id')) {
                $validated['tenant_id'] = session('current_tenant_id');
            }
        }

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

            // Clear existing widgets for the areas being updated
            $areas = collect($widgets)->pluck('area')->unique();
            foreach ($areas as $area) {
                Widget::where('area', $area)->delete();
            }

            // Save new widgets
            foreach ($widgets as $widgetData) {
                try {
                    $validated = $this->validateWidgetData($widgetData);
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
        
        $count = Widget::where('area', $area)->count();
        Widget::where('area', $area)->delete();
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
            'is_active' => 'nullable|boolean'
        ];

        $validator = \Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        $validated = $validator->validated();

        // Process settings
        if (isset($validated['settings']) && is_string($validated['settings'])) {
            $validated['settings'] = json_decode($validated['settings'], true);
        }

        // Set defaults
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Set tenant_id for project context
        $projectCode = request()->route('projectCode');
        if ($projectCode && session('current_tenant_id')) {
            $validated['tenant_id'] = session('current_tenant_id');
        }

        return $validated;
    }
}
