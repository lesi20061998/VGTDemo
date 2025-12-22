<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugOrders extends Command
{
    protected $signature = 'debug:orders {projectCode}';

    protected $description = 'Debug orders for a project';

    public function handle(): int
    {
        $projectCode = $this->argument('projectCode');
        $projectDbName = 'project_'.$projectCode;

        // Set up project database connection
        config(['database.connections.project.database' => $projectDbName]);
        DB::purge('project');

        $this->info("🔍 Debugging orders for project: {$projectCode}");
        $this->info("Database: {$projectDbName}");

        // Check raw table data
        $rawOrders = DB::connection('project')->table('orders')
            ->select('id', 'order_number', 'project_id', 'tenant_id')
            ->limit(5)
            ->get();

        $this->info('📊 Raw table data (first 5 orders):');
        foreach ($rawOrders as $order) {
            $this->line("ID: {$order->id}, Order: {$order->order_number}, Project ID: {$order->project_id}, Tenant ID: {$order->tenant_id}");
        }

        // Check with model (without scope)
        $this->info("\n🔧 Testing model queries:");

        // Bypass project scope temporarily
        config(['app.bypass_project_scope' => true]);
        $modelCount = Order::count();
        $this->info("Model count (scope bypassed): {$modelCount}");

        // Reset scope
        config(['app.bypass_project_scope' => false]);
        $scopedCount = Order::count();
        $this->info("Model count (with scope): {$scopedCount}");

        // Check current project context
        $project = request()->attributes->get('project');
        $this->info('Current project in request: '.($project ? $project->id : 'null'));

        return Command::SUCCESS;
    }
}
