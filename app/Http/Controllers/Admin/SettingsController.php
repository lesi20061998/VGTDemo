<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $project = request()->attributes->get('project');
        $user = auth()->user();

        if ($project) {
            \DB::setDefaultConnection('mysql');

            $enabledSettings = \App\Models\ProjectSetting::where('project_id', $project->id)
                ->where('value', '1')
                ->pluck('key')
                ->toArray();

            \DB::setDefaultConnection('project');

            // Chỉ hiển thị các module đã được bật
            $modules = collect(config('system_menu'))->filter(function ($module) use ($enabledSettings) {
                return in_array($module['permission'], $enabledSettings);
            });
        } else {
            // SuperAdmin (level 0 hoặc 1) có quyền truy cập tất cả
            if ($user && ($user->level === 0 || $user->level === 1)) {
                $modules = collect(config('system_menu'));
            } else {
                // User thường chỉ thấy các module được phép
                $modules = collect(config('system_menu'));
            }
        }

        return view('cms.settings.index', compact('modules'));
    }

    public function scanTranslations()
    {
        try {
            $keys = [];
            $viewPath = resource_path('views');

            // Scan all blade files
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($viewPath)
            );

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $content = file_get_contents($file->getRealPath());

                    // Match __('key') and __("key") patterns
                    preg_match_all("/__\('([^']+)'\)/", $content, $matches1);
                    preg_match_all('/__\("([^"]+)"\)/', $content, $matches2);

                    if (! empty($matches1[1])) {
                        $keys = array_merge($keys, $matches1[1]);
                    }
                    if (! empty($matches2[1])) {
                        $keys = array_merge($keys, $matches2[1]);
                    }
                }
            }

            $keys = array_unique($keys);
            sort($keys);

            return response()->json([
                'success' => true,
                'keys' => $keys,
                'count' => count($keys),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            // Debug: Log all request data
            \Log::info('Settings save - Request data:', [
                'all_data' => $request->all(),
                'watermark_data' => $request->input('watermark'),
                'url' => $request->url(),
                'method' => $request->method(),
            ]);

            $project = $request->attributes->get('project');

            foreach ($request->except('_token', '_method', 'page') as $key => $value) {
                // Xử lý đặc biệt cho checkbox
                if ($key === 'watermark' && is_array($value)) {
                    // Đảm bảo enabled được xử lý đúng
                    if (! isset($value['enabled'])) {
                        $value['enabled'] = false;
                    } else {
                        $value['enabled'] = $value['enabled'] === '1' || $value['enabled'] === 1 || $value['enabled'] === true;
                    }
                }

                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $value = $decoded;
                    }
                }

                // Sử dụng model phù hợp dựa trên context
                if ($project) {
                    \App\Models\ProjectSettingModel::set($key, $value);
                } else {
                    \App\Models\Setting::set($key, $value);
                }
            }

            \App\Services\SettingsService::getInstance()->clearCache();

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Lưu cấu hình thành công! Đã lưu '.count($request->except('_token', '_method', 'page')).' cài đặt.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Settings save error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }

    public function projectSettings(Request $request)
    {
        $project = $request->attributes->get('project');

        $mainDb = config('database.connections.mysql.database');
        \DB::setDefaultConnection('mysql');

        $enabledSettings = \App\Models\ProjectSetting::where('project_id', $project->id)
            ->where('value', '1')
            ->pluck('key')
            ->toArray();

        \DB::setDefaultConnection('project');

        // Chỉ hiển thị các module đã được bật
        $modules = collect(config('system_menu'))
            ->filter(function ($module) use ($enabledSettings) {
                return in_array($module['permission'], $enabledSettings);
            })
            ->map(function ($module) use ($project) {
                $module['route'] = str_replace('cms.', 'project.admin.', $module['route']);
                $module['route_params'] = ['projectCode' => $project->code];

                return $module;
            })
            ->filter(function ($module) {
                return \Route::has($module['route']);
            });

        return view('cms.settings.index', [
            'currentProject' => $project,
            'modules' => $modules,
        ]);
    }

    public function saveProjectSettings(Request $request)
    {
        $project = $request->attributes->get('project');

        try {
            if ($request->has('permissions')) {
                foreach ($request->permissions as $module => $perms) {
                    $project->permissions()->updateOrCreate(
                        ['module' => $module],
                        $perms
                    );
                }
            }

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Cập nhật phân quyền thành công!',
            ]);
        } catch (\Exception $e) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }
}
