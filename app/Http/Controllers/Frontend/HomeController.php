<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Get current project from session
        $currentProject = session('current_project');
        $theme = null;
        
        if ($currentProject) {
            // Check if project has a theme setting
            $projectId = is_array($currentProject) ? ($currentProject['id'] ?? null) : ($currentProject->id ?? null);
            if ($projectId) {
                $theme = \App\Models\Setting::where('tenant_id', $projectId)
                    ->where('key', 'theme')
                    ->value('value');
            }
        }

        // Use theme-specific view if available
        if ($theme && view()->exists("frontend.themes.{$theme}.home")) {
            return view("frontend.themes.{$theme}.home");
        }
        
        // Check for victorious theme as default
        if (view()->exists('frontend.themes.victorious.home')) {
            return view('frontend.themes.victorious.home');
        }

        // Fallback to default home view
        return view('frontend.home');
    }
}
