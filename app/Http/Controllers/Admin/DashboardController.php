<?php
// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VisitorLog;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = CacheService::remember(CacheService::DASHBOARD_STATS, 5, function () {
            return [
                'today_orders' => rand(5, 25),
                'today_revenue' => rand(2000000, 8000000),
                'total_revenue' => rand(50000000, 150000000),
                'out_of_stock_products' => rand(2, 15),
                'new_users_today' => rand(3, 18),
                'total_users' => rand(150, 500),
                'total_products' => rand(80, 300),
                'pending_orders' => rand(5, 25),
                
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
                'revenue' => $revenue
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
                'orders' => $orders
            ]);
        }
        return $days;
    }

    private function getDeviceChart()
    {
        // Simulate device data
        return collect([
            ['device' => 'Desktop', 'percentage' => 45, 'color' => '#3B82F6'],
            ['device' => 'Mobile', 'percentage' => 35, 'color' => '#10B981'],
            ['device' => 'Tablet', 'percentage' => 20, 'color' => '#F59E0B']
        ]);
    }

    private function getTrafficChart()
    {
        // Simulate traffic data
        return collect([
            ['source' => 'Organic Search', 'visitors' => 1250, 'percentage' => 42],
            ['source' => 'Direct', 'visitors' => 890, 'percentage' => 30],
            ['source' => 'Social Media', 'visitors' => 520, 'percentage' => 18],
            ['source' => 'Referral', 'visitors' => 298, 'percentage' => 10]
        ]);
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
        
        $data = [
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
            'visitor_stats' => $this->getVisitorStats(),
            'top_ips' => VisitorLog::getTopIPs(10),
            'currentProject' => $project
        ];

        return view('cms.dashboard.index', $data);
    }

}
