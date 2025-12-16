<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

class WidgetTemplateController extends Controller
{
    public function index()
    {
        $widgets = WidgetRegistry::getByCategory();
        return view('cms.widget-templates.index', compact('widgets'));
    }

    public function show($type)
    {
        $widget = WidgetRegistry::getConfig($type);
        if (!$widget) {
            abort(404);
        }
        return view('cms.widget-templates.show', compact('widget'));
    }

    public function preview($type, Request $request)
    {
        $settings = $request->all();
        $html = WidgetRegistry::render($type, $settings);
        return response()->json(['html' => $html]);
    }
}