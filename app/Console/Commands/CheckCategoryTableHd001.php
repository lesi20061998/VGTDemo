<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCategoryTableHd001 extends Command
{
    protected $signature = 'check:category-hd001';

    protected $description = 'Check product_categories table structure in project_hd001';

    public function handle(): int
    {
        $this->info('Checking product_categories table in project_hd001...');

        try {
            // Check if product_categories table exists in project_hd001
            $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'project_hd001' AND TABLE_NAME = 'product_categories'");

            if (empty($tables)) {
                $this->error('❌ product_categories table does NOT exist in project_hd001');

                return 1;
            }

            $this->info('✅ product_categories table exists in project_hd001');

            // Check table structure
            $this->info("\n=== Product Categories table structure ===");
            $columns = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'project_hd001' 
                AND TABLE_NAME = 'product_categories' 
                ORDER BY ORDINAL_POSITION
            ");

            $hasMetaTitle = false;
            $hasMetaDesc = false;

            foreach ($columns as $column) {
                $nullable = $column->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $column->COLUMN_DEFAULT ? "DEFAULT {$column->COLUMN_DEFAULT}" : '';

                if ($column->COLUMN_NAME === 'meta_title') {
                    $hasMetaTitle = true;
                    $this->line("✅ {$column->COLUMN_NAME} ({$column->DATA_TYPE}) {$nullable} {$default}");
                } elseif ($column->COLUMN_NAME === 'meta_description') {
                    $hasMetaDesc = true;
                    $this->line("✅ {$column->COLUMN_NAME} ({$column->DATA_TYPE}) {$nullable} {$default}");
                } else {
                    $this->line("- {$column->COLUMN_NAME} ({$column->DATA_TYPE}) {$nullable} {$default}");
                }
            }

            $this->info("\n=== Meta Fields Status ===");
            $this->line('meta_title: '.($hasMetaTitle ? '✅ EXISTS' : '❌ MISSING'));
            $this->line('meta_description: '.($hasMetaDesc ? '✅ EXISTS' : '❌ MISSING'));

            if (! $hasMetaTitle || ! $hasMetaDesc) {
                $this->warn("\n⚠️  Meta fields are missing. Need to add them to project_hd001.product_categories table.");
                $this->info('Run: php artisan add:category-meta-hd001');
            } else {
                $this->info("\n✅ All meta fields exist in project_hd001.product_categories table!");
            }

            // Show sample data
            $this->info("\n=== Sample category data ===");
            $sampleData = DB::select('
                SELECT id, name, slug, level, parent_id
                FROM project_hd001.product_categories 
                LIMIT 5
            ');

            if (empty($sampleData)) {
                $this->line('No categories found in database');
            } else {
                foreach ($sampleData as $category) {
                    $this->line("ID: {$category->id} | Name: {$category->name} | Slug: {$category->slug} | Level: {$category->level} | Parent: ".($category->parent_id ?? 'NULL'));
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
