<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectOrderStats extends Command
{
    protected $signature = 'project:order-stats {projectCode : The project code (e.g., hd001)}';

    protected $description = 'Show order statistics for a specific project';

    public function handle(): int
    {
        $projectCode = $this->argument('projectCode');
        $projectDbName = 'project_'.$projectCode;

        try {
            config(['database.connections.project.database' => $projectDbName]);
            DB::purge('project');

            $this->info("📊 Order Statistics for Project: {$projectCode}");
            $this->newLine();

            // Total orders
            $totalOrders = DB::connection('project')->table('orders')->count();
            $totalRevenue = DB::connection('project')->table('orders')->sum('total_amount');

            // Orders by status
            $ordersByStatus = DB::connection('project')
                ->table('orders')
                ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as revenue'))
                ->groupBy('status')
                ->get();

            // Orders by payment status
            $ordersByPayment = DB::connection('project')
                ->table('orders')
                ->select('payment_status', DB::raw('count(*) as count'))
                ->groupBy('payment_status')
                ->get();

            // Recent orders
            $recentOrders = DB::connection('project')
                ->table('orders')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['order_number', 'customer_name', 'total_amount', 'status', 'created_at']);

            // Display summary
            $this->table(['Metric', 'Value'], [
                ['Total Orders', number_format($totalOrders)],
                ['Total Revenue', number_format($totalRevenue, 0).' VND'],
                ['Average Order Value', number_format($totalRevenue / max($totalOrders, 1), 0).' VND'],
            ]);

            $this->newLine();
            $this->info('📦 Orders by Status:');
            $statusData = $ordersByStatus->map(function ($item) {
                return [
                    'Status' => ucfirst($item->status),
                    'Count' => $item->count,
                    'Revenue' => number_format($item->revenue, 0).' VND',
                ];
            })->toArray();
            $this->table(['Status', 'Count', 'Revenue'], $statusData);

            $this->newLine();
            $this->info('💳 Orders by Payment Status:');
            $paymentData = $ordersByPayment->map(function ($item) {
                return [
                    'Payment Status' => ucfirst($item->payment_status),
                    'Count' => $item->count,
                ];
            })->toArray();
            $this->table(['Payment Status', 'Count'], $paymentData);

            $this->newLine();
            $this->info('🕐 Recent Orders:');
            $recentData = $recentOrders->map(function ($item) {
                return [
                    'Order #' => $item->order_number,
                    'Customer' => $item->customer_name,
                    'Amount' => number_format($item->total_amount, 0).' VND',
                    'Status' => ucfirst($item->status),
                    'Date' => date('Y-m-d H:i', strtotime($item->created_at)),
                ];
            })->toArray();
            $this->table(['Order #', 'Customer', 'Amount', 'Status', 'Date'], $recentData);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
