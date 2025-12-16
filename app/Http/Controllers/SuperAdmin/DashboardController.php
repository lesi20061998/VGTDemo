<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\Project;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $totalContracts = Contract::count();
        $pendingContracts = Contract::where('is_active', false)->count();
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();

        return view('superadmin.dashboard.index', compact(
            'totalEmployees',
            'totalContracts',
            'pendingContracts',
            'totalProjects',
            'activeProjects'
        ));
    }

    public function multiTenancy()
    {
        try {
            $projects = Project::with(['contract', 'admin'])->latest()->get();
            
            // SuperAdmin có thể xem tất cả activities
            $todayActivities = ActivityLog::whereDate('created_at', today())->count();
            $recentActivities = ActivityLog::with(['user', 'project'])
                ->latest()
                ->take(10)
                ->get();

            return view('superadmin.dashboard.multi-tenancy', compact(
                'projects',
                'todayActivities', 
                'recentActivities'
            ));
            
        } catch (\Exception $e) {
            \Log::error('MultiTenancy dashboard error: ' . $e->getMessage());
            
            // Fallback data nếu có lỗi
            $projects = Project::latest()->get();
            $todayActivities = 0;
            $recentActivities = collect();
            
            return view('superadmin.dashboard.multi-tenancy', compact(
                'projects',
                'todayActivities',
                'recentActivities'
            ))->with('alert', [
                'type' => 'warning',
                'message' => 'Một số dữ liệu không thể tải được. Vui lòng chạy migration cho các project.'
            ]);
        }
    }
}

