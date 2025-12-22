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

        return view('cms.widgets.builder', compact('existingWidgets', 'availableWidgets'));
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
}
