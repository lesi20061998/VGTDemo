<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VisitorLog;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = CacheService::remember(CacheService::DASHBOARD_STATS, 5, function () {
            return [
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'today_revenue' => Order::whereDate('created_at', today())->sum('total_amount'),
                'total_revenue' => Order::sum('total_amount'),
                'out_of_stock_products' => Product::where('stock_status', 'out_of_stock')->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'total_users' => User::count(),
                'total_products' => Product::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),

                'revenue_chart' => $this->getRevenueChart(),
                'orders_chart' => $this->getOrdersChart(),
                'device_chart' => $this->getDeviceChart(),
                'traffic_chart' => $this->getTrafficChart(),
                'order_status_chart' => $this->getOrderStatusChart(),
                'top_products' => $this->getTopProducts(),
                'recent_orders' => Order::with(['items'])->latest()->take(5)->get(),
            ];
        });

        $data['visitor_stats'] = CacheService::remember(CacheService::VISITOR_STATS, 2, function () {
            return $this->getVisitorStats();
        });

        $data['top_ips'] = CacheService::remember(CacheService::TOP_IPS, 10, function () {
            return VisitorLog::getTopIPs(10);
        });

        return view('cms.dashboard.index', $data);
    }

    private function getRevenueChart()
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)->sum('total_amount');
            $days->push([
                'date' => $date->format('d/m'),
                'revenue' => $revenue,
            ]);
        }

        return $days;
    }

    private function getOrdersChart()
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $orders = Order::whereDate('created_at', $date)->count();
            $days->push([
                'date' => $date->format('d/m'),
                'orders' => $orders,
            ]);
        }

        return $days;
    }

    private function getDeviceChart()
    {
        // Get real device data from visitor logs
        $deviceData = Cache::remember('device_analytics', 300, function () {
            $totalVisits = VisitorLog::where('visited_at', '>=', now()->subDays(30))->count();

            if ($totalVisits == 0) {
                // Fallback to sample data if no visitor logs
                return collect([
                    ['device' => 'Desktop', 'percentage' => 45, 'color' => '#3B82F6'],
                    ['device' => 'Mobile', 'percentage' => 35, 'color' => '#10B981'],
                    ['device' => 'Tablet', 'percentage' => 20, 'color' => '#F59E0B'],
                ]);
            }

            $devices = VisitorLog::select(DB::raw('
                CASE 
                    WHEN user_agent LIKE "%Mobile%" OR user_agent LIKE "%Android%" OR user_agent LIKE "%iPhone%" THEN "Mobile"
                    WHEN user_agent LIKE "%Tablet%" OR user_agent LIKE "%iPad%" THEN "Tablet"
                    ELSE "Desktop"
                END as device_type,
                COUNT(*) as count
            '))
                ->where('visited_at', '>=', now()->subDays(30))
                ->groupBy('device_type')
                ->get();

            $colors = ['Desktop' => '#3B82F6', 'Mobile' => '#10B981', 'Tablet' => '#F59E0B'];

            return $devices->map(function ($device) use ($totalVisits, $colors) {
                return [
                    'device' => $device->device_type,
                    'percentage' => round(($device->count / $totalVisits) * 100),
                    'color' => $colors[$device->device_type] ?? '#6B7280',
                ];
            });
        });

        return $deviceData;
    }

    private function getTrafficChart()
    {
        // Get real traffic source data from visitor logs
        return Cache::remember('traffic_analytics', 300, function () {
            $totalVisits = VisitorLog::where('visited_at', '>=', now()->subDays(30))->count();

            if ($totalVisits == 0) {
                // Fallback to sample data if no visitor logs
                return collect([
                    ['source' => 'Direct', 'visitors' => 0, 'percentage' => 100],
                ]);
            }

            // Analyze referrer patterns from URL and user agent
            $sources = VisitorLog::select(DB::raw('
                CASE 
                    WHEN url LIKE "%utm_source=google%" OR user_agent LIKE "%Googlebot%" THEN "Google Search"
                    WHEN url LIKE "%utm_source=facebook%" OR url LIKE "%facebook%" THEN "Facebook"
                    WHEN url LIKE "%utm_source=%" THEN "Social Media"
                    WHEN url LIKE "%ref=%" THEN "Referral"
                    ELSE "Direct"
                END as source_type,
                COUNT(*) as count
            '))
                ->where('visited_at', '>=', now()->subDays(30))
                ->groupBy('source_type')
                ->orderBy('count', 'desc')
                ->get();

            return $sources->map(function ($source) use ($totalVisits) {
                return [
                    'source' => $source->source_type,
                    'visitors' => $source->count,
                    'percentage' => round(($source->count / $totalVisits) * 100),
                ];
            });
        });
    }

    private function getOrderStatusChart()
    {
        return Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            })
            ->toArray();
    }

    private function getTopProducts()
    {
        return DB::table('order_items')
            ->join('products_enhanced', 'order_items.product_id', '=', 'products_enhanced.id')
            ->select('products_enhanced.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products_enhanced.id', 'products_enhanced.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();
    }

    private function getVisitorStats()
    {
        return VisitorLog::getTodayStats();
    }

    public function stats(Request $request)
    {
        $stats = Cache::remember('dashboard_api_stats', 30, function () {
            return [
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'revenue_today' => Order::whereDate('created_at', today())->sum('total_amount'),
                'products_out_of_stock' => Product::where('stock_status', 'out_of_stock')->count(),
                'users_new' => User::whereDate('created_at', today())->count(),
            ];
        });

        return response()->json($stats);
    }

    public function projectDashboard(Request $request)
    {
        $project = $request->attributes->get('project');

        // Cache the dashboard data for better performance
        $data = Cache::remember("project_dashboard_{$project->id}", 5, function () {
            return [
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'today_revenue' => Order::whereDate('created_at', today())->sum('total_amount'),
                'total_revenue' => Order::sum('total_amount'),
                'out_of_stock_products' => Product::where('stock_status', 'out_of_stock')->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'total_users' => User::count(),
                'total_products' => Product::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),

                'revenue_chart' => $this->getRevenueChart(),
                'orders_chart' => $this->getOrdersChart(),
                'order_status_chart' => $this->getOrderStatusChart(),
                'top_products' => $this->getTopProducts(),
                'recent_orders' => Order::with(['items'])->latest()->take(5)->get(),
            ];
        });

        // Add real-time data that shouldn't be cached
        $data['device_chart'] = $this->getDeviceChart();
        $data['traffic_chart'] = $this->getTrafficChart();
        $data['visitor_stats'] = $this->getVisitorStats();
        $data['top_ips'] = VisitorLog::getTopIPs(10);
        $data['currentProject'] = $project;

        return view('cms.dashboard.index', $data);
    }
}
