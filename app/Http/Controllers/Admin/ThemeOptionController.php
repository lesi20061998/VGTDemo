<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ThemeOptionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'layout');
        $project = $request->attributes->get('project');
        
        if ($project) {
            // Project context - load from main database with project_id
            $settings = \DB::table('settings')
                ->where('key', "theme_option_{$tab}")
                ->where('project_id', $project->id)
                ->first();
            $data = $settings ? json_decode($settings->payload, true) : [];
        } else {
            // Global context
            $settings = Setting::where('key', "theme_option_{$tab}")->first();
            $data = $settings ? $settings->payload : [];
        }

        return view('cms.theme-options.index', compact('tab', 'data'));
    }

    public function update(Request $request)
    {
        $tab = $request->get('tab', 'layout');
        $data = $request->except(['_token', '_method', 'tab']);
        $project = $request->attributes->get('project');

        \Log::info('Theme options update', [
            'tab' => $tab,
            'data' => $data,
            'key' => "theme_option_{$tab}",
            'project_id' => $project?->id,
        ]);

        if ($project) {
            // Project context - save to main database with project_id
            \DB::table('settings')
                ->where('key', "theme_option_{$tab}")
                ->where('project_id', $project->id)
                ->delete();
                
            \DB::table('settings')->insert([
                'key' => "theme_option_{$tab}",
                'payload' => json_encode($data),
                'group' => 'theme',
                'project_id' => $project->id,
                'tenant_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Global context
            $tenantId = session('current_tenant_id');
            Setting::updateOrCreate(
                ['key' => "theme_option_{$tab}", 'tenant_id' => $tenantId],
                ['payload' => $data]
            );
        }

        // Clear cache
        \Cache::forget('all_settings_main');
        \App\Services\SettingsService::getInstance()->clearCache();

        return redirect()->back()->with('success', 'Đã lưu cấu hình!');
    }
}
