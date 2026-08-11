<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\Post;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Nếu là Developer, hiển thị Dashboard dành riêng cho Dev
        if ($user->role === 'dev' || $user->hasRole('dev')) {
            return $this->devDashboard($user);
        }

        $totalEmployees = User::count();
        $totalContracts = Post::where('post_type', 'contract')->count();
        $pendingContracts = Post::where('post_type', 'contract')->where('status', 'draft')->count();
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();

        // Doanh thu dự kiến tháng này (dựa trên hợp đồng được tạo trong tháng)
        $expectedRevenue = Contract::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('contract_value');

        // Các dự án sắp trễ hạn (deadline trong vòng 2 ngày tới hoặc đã qua) và chưa hoàn thành
        $urgentProjectsRaw = Project::with('tasks')
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(2))
            ->orderBy('deadline', 'asc')
            ->get();

        $urgentProjects = $urgentProjectsRaw->filter(function ($project) {
            $totalTasks = $project->tasks->count();
            if ($totalTasks === 0) {
                return true;
            } // Giữ lại nếu chưa có task nào (chưa hoàn thành)

            $completedTasks = $project->tasks->where('status', 'completed')->count();

            return $completedTasks < $totalTasks; // Giữ lại nếu số task hoàn thành < tổng số task
        })->values();

        // Tiến độ các dự án đang hoạt động
        $projectProgresses = Project::with('tasks')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($project) {
                $totalTasks = $project->tasks->count();
                $completedTasks = $project->tasks->where('status', 'completed')->count();
                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                $project->progress = $progress;
                $project->totalTasks = $totalTasks;
                $project->completedTasks = $completedTasks;

                return $project;
            });

        // Tài nguyên Web (Domain & Hosting) sắp hết hạn (trong vòng 1 tháng hoặc đã quá hạn gần đây)
        $expiringWebResources = Contract::where(function ($query) {
            $query->whereNotNull('domain_name')
                ->orWhereNotNull('hosting_provider');
        })
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addMonth())
            ->where('status', '!=', 'cancelled')
            ->orderBy('end_date', 'asc')
            ->get();

        $allProjects = Project::all();
        $infectedProjects = [];
        foreach ($allProjects as $p) {
            $logPath = storage_path('logs/file-changes-'.$p->code.'.log');
            if (File::exists($logPath)) {
                $content = File::get($logPath);
                if (str_contains($content, 'Độc Hại') || str_contains($content, '\u0110\u1ed9c H\u1ea1i')) {
                    $infectedProjects[] = $p;
                }
            }
        }

        return view('superadmin.dashboard.index', compact(
            'totalEmployees',
            'totalContracts',
            'pendingContracts',
            'totalProjects',
            'activeProjects',
            'expectedRevenue',
            'urgentProjects',
            'projectProgresses',
            'expiringWebResources',
            'infectedProjects'
        ));
    }

    private function devDashboard($user)
    {
        // Thống kê Tasks
        $totalAssignedTasks = Task::where('dev_id', $user->id)->count();

        $completedTasks = Task::where('dev_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $pendingTasks = Task::where('dev_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        // Danh sách công việc sắp trễ hạn / quá hạn (deadline <= 2 ngày tới, chưa completed)
        $urgentTasks = Task::with('project')
            ->where('dev_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(2))
            ->orderBy('deadline', 'asc')
            ->take(10)
            ->get();

        // Tiến độ các dự án đang tham gia (Dựa vào task của dev trong dự án)
        // Lấy tất cả project_ids từ tasks của dev
        $projectIds = Task::where('dev_id', $user->id)->distinct()->pluck('project_id');

        $projectProgresses = Project::with('tasks')
            ->whereIn('id', $projectIds)
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($project) {
                $totalTasks = $project->tasks->count();
                $completedTasks = $project->tasks->where('status', 'completed')->count();
                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                $project->progress = $progress;
                $project->totalTasks = $totalTasks;
                $project->completedTasks = $completedTasks;

                return $project;
            });

        return view('superadmin.dashboard.dev', compact(
            'totalAssignedTasks',
            'completedTasks',
            'pendingTasks',
            'urgentTasks',
            'projectProgresses'
        ));
    }

    public function multiTenancy()
    {
        try {
            $projects = Project::with(['admin'])->latest()->get();

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
            \Log::error('MultiTenancy dashboard error: '.$e->getMessage());

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
                'message' => 'Một số dữ liệu không thể tải được. Vui lòng chạy migration cho các project.',
            ]);
        }
    }
}
