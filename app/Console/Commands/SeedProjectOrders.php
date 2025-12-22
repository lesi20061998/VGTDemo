<?php

namespace App\Console\Commands;

use Database\Seeders\OrderSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedProjectOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:seed-orders {projectCode : The project code (e.g., hd001)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed orders for a specific project';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $projectCode = $this->argument('projectCode');

        $this->info("🌱 Seeding orders for project: {$projectCode}");

        // Validate project exists
        $projectDbName = 'project_'.$projectCode;

        try {
            // Test connection to project database
            config(['database.connections.project.database' => $projectDbName]);
            DB::purge('project');
            DB::connection('project')->getPdo();

            $this->info("✅ Connected to database: {$projectDbName}");

        } catch (\Exception $e) {
            $this->error("❌ Cannot connect to project database: {$projectDbName}");
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }

        // Run the seeder
        try {
            $seeder = new OrderSeeder;
            $seeder->setCommand($this);
            $seeder->seedForProject($projectCode);

            $this->info("🎉 Successfully seeded orders for project {$projectCode}");

            // Show summary
            $orderCount = DB::connection('project')->table('orders')->count();
            $orderItemCount = DB::connection('project')->table('order_items')->count();

            $this->table(['Metric', 'Count'], [
                ['Orders Created', $orderCount],
                ['Order Items Created', $orderItemCount],
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to seed orders: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
