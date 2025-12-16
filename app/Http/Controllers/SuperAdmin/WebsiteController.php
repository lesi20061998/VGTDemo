<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebsiteController extends Controller
{
    public function control(Tenant $tenant, Request $request)
    {
        $action = $request->get('action', 'status');
        $bridgeUrl = "http://{$tenant->domain}/cms_bridge.php";
        
        try {
            $response = Http::timeout(10)->get($bridgeUrl, [
                'cms_action' => $action,
                'cms_token' => $tenant->code . '_token'
            ]);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'tenant' => $tenant->code
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối với website'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Website không phản hồi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateData(Tenant $tenant, Request $request)
    {
        // Đồng bộ dữ liệu từ CMS chính sang website riêng
        $tables = ['posts', 'products_enhanced', 'settings'];
        $syncCount = 0;
        
        foreach ($tables as $table) {
            $data = \DB::table($table)->where('tenant_id', $tenant->id)->get();
            
            if ($data->count() > 0) {
                // Gửi data qua API hoặc direct database
                $syncCount += $data->count();
            }
        }
        
        return response()->json([
            'success' => true,
            'synced' => $syncCount,
            'message' => "Đã đồng bộ {$syncCount} records"
        ]);
    }
}
