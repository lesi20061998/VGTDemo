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
            ->map(fn($widgets) => $widgets->map(fn($w) => [
                'type' => $w->type,
                'name' => $w->name,
                'area' => $w->area,
                'sort_order' => $w->sort_order,
                'is_active' => $w->is_active,
                'settings' => $w->settings
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
        if (!empty($validated['config'])) {
            $validated['settings'] = array_merge($validated['settings'] ?? [], ['config' => $validated['config']]);
        }
        unset($validated['config']);

        Widget::create($validated);
        clear_widget_cache($validated['area']);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
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
        
        return redirect()->route('cms.widgets.index')->with('success', 'Widget updated successfully');
    }

    public function destroy(Widget $widget)
    {
        $area = $widget->area;
        $widget->delete();
        clear_widget_cache($area);
        
        return redirect()->route('cms.widgets.index')->with('success', 'Widget deleted successfully');
    }
}

