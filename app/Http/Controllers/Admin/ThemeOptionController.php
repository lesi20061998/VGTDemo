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
        $settings = Setting::where('key', "theme_option_{$tab}")->first();
        $data = $settings ? $settings->payload : [];

        return view('cms.theme-options.index', compact('tab', 'data'));
    }

    public function update(Request $request)
    {
        $tab = $request->get('tab', 'layout');
        $data = $request->except(['_token', '_method', 'tab']);

        \Log::info('Theme options update', [
            'tab' => $tab,
            'data' => $data,
            'key' => "theme_option_{$tab}",
        ]);

        $tenantId = session('current_tenant_id');

        Setting::updateOrCreate(
            ['key' => "theme_option_{$tab}", 'tenant_id' => $tenantId],
            ['payload' => $data]
        );

        // Clear cache
        $tenantId = session('current_tenant_id');
        \Cache::forget('all_settings_'.$tenantId);
        \App\Services\SettingsService::getInstance()->clearCache();

        return redirect()->back()->with('success', 'Đã lưu cấu hình!');
    }
}
