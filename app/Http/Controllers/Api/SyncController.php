<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Nhận danh sách widgets từ SuperAdmin và cập nhật nội bộ
     */
    public function syncWidgets(Request $request)
    {
        $payload = $request->input('data', []);
        $projectId = env('PROJECT_ID'); // Có thể null trong một số trường hợp, tùy logic

        if (empty($payload)) {
            return response()->json(['success' => false, 'message' => 'No data provided'], 400);
        }

        try {
            DB::beginTransaction();

            // Logic đồng bộ widget:
            // Tùy theo cấu trúc payload từ SuperAdmin, ví dụ ta xóa hết widget hiện tại của site và insert lại mới.
            // Hoặc update từng record.
            // Giả sử payload truyền sang mảng các widget:
            foreach ($payload as $widgetData) {
                // Đảm bảo không bị lỗi về ID nếu insert
                DB::table('widgets')->updateOrInsert(
                    ['id' => $widgetData['id']],
                    $widgetData
                );
            }

            DB::commit();

            // Clear cache widget nếu có
            Cache::tags(['widgets'])->flush();

            return response()->json(['success' => true, 'message' => 'Widgets synced successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync widgets failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Nhận danh sách settings từ SuperAdmin và cập nhật nội bộ
     */
    public function syncSettings(Request $request)
    {
        $payload = $request->input('data', []);

        if (empty($payload)) {
            return response()->json(['success' => false, 'message' => 'No data provided'], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($payload as $settingData) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $settingData['key']], // Dựa vào key để update
                    $settingData
                );
            }

            DB::commit();

            // Clear cache settings
            Cache::forget('global_settings');

            return response()->json(['success' => true, 'message' => 'Settings synced successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync settings failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
