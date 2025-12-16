<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixMultisiteDatabase extends Command
{
    protected $signature = 'multisite:fix-database';
    protected $description = 'Fix database issues for multisite projects';

    public function handle()
    {
        $this->info('Fixing multisite database issues...');
        
        // Kiểm tra và thêm project_id cho các bảng
        $tables = [
            'products_enhanced',
            'product_categories', 
            'brands',
            'orders',
            'menus',
            'widgets',
            'settings',
            'posts',
            'pages'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                if (!Schema::hasColumn($table, 'project_id')) {
                    $this->info("Adding project_id to {$table}...");
                    
                    try {
                        Schema::table($table, function ($table_blueprint) {
                            $table_blueprint->unsignedBigInteger('project_id')->nullable();
                            $table_blueprint->index('project_id');
                        });
                        
                        $this->info("✅ Added project_id to {$table}");
                    } catch (\Exception $e) {
                        $this->error("❌ Failed to add project_id to {$table}: " . $e->getMessage());
                    }
                } else {
                    $this->info("✓ {$table} already has project_id");
                }
            } else {
                $this->warn("⚠️ Table {$table} does not exist");
            }
        }
        
        $this->info('Database fix completed!');
    }
}