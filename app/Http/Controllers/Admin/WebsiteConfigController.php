<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteConfigController extends Controller
{
    public function index(Request $request)
    {
        $sections = config('website_sections');
        $activeTab = $request->get('tab', 'general');

        $settings = [];
        foreach ($sections as $section) {
            foreach ($section['fields'] as $fieldKey => $field) {
                $settings[$fieldKey] = setting($fieldKey, $field['default'] ?? '');
            }
        }

        $menus = \App\Models\Menu::all();

        return view('cms.website-config.index', compact('sections', 'activeTab', 'settings', 'menus'));
    }

    public function save(Request $request)
    {
        try {
            $sections = config('website_sections');
            $activeTab = $request->get('tab', 'general');

            if (isset($sections[$activeTab])) {
                foreach ($sections[$activeTab]['fields'] as $fieldKey => $field) {
                    if ($request->hasFile($fieldKey)) {
                        $file = $request->file($fieldKey);
                        $path = $file->store('website-config', 'public');
                        $value = '/storage/'.$path;
                    } elseif ($field['type'] === 'checkbox') {
                        $value = $request->input($fieldKey, 0);
                    } else {
                        $value = $request->input($fieldKey, '');
                    }

                    // Sử dụng model phù hợp dựa trên context
                    $project = $request->attributes->get('project');
                    if ($project) {
                        \App\Models\ProjectSettingModel::set($fieldKey, $value);
                    } else {
                        \App\Models\Setting::set($fieldKey, $value);
                    }
                }
            }

            $tenantId = session('current_tenant_id');
            \Cache::forget('all_settings_'.$tenantId);
            \App\Services\SettingsService::getInstance()->clearCache();

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Lưu cấu hình thành công!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Website config save error: '.$e->getMessage());

            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }

    public function preview()
    {
        return view('cms.website-config.preview');
    }
}
