<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckBrandTableStructureDetailed extends Command
{
    protected $signature = 'check:brand-table-detailed';

    protected $description = 'Check detailed brand table structure in project database';

    public function handle(): int
    {
        $this->info('Checking brand table structure in project_hd001 database...');

        try {
            // Switch to project connection
            $connection = 'project';

            // Check if brands table exists
            if (! Schema::connection($connection)->hasTable('brands')) {
                $this->error('Brands table does not exist in project database!');

                return 1;
            }

            // Get all columns from brands table
            $columns = DB::connection($connection)->select('DESCRIBE brands');

            $this->info("\nCurrent brands table structure:");
            $this->table(
                ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'],
                collect($columns)->map(function ($column) {
                    return [
                        $column->Field,
                        $column->Type,
                        $column->Null,
                        $column->Key ?? '',
                        $column->Default ?? 'NULL',
                        $column->Extra ?? '',
                    ];
                })->toArray()
            );

            // Expected columns for a complete brand table
            $expectedColumns = [
                'id' => 'Primary key',
                'name' => 'Brand name (required)',
                'slug' => 'URL slug (optional)',
                'description' => 'Brand description (optional)',
                'logo' => 'Logo URL (optional)',
                'is_active' => 'Active status (boolean)',
                'meta_title' => 'SEO title (optional)',
                'meta_description' => 'SEO description (optional)',
                'created_at' => 'Creation timestamp',
                'updated_at' => 'Update timestamp',
            ];

            $existingColumns = collect($columns)->pluck('Field')->toArray();
            $missingColumns = array_diff(array_keys($expectedColumns), $existingColumns);
            $extraColumns = array_diff($existingColumns, array_keys($expectedColumns));

            if (! empty($missingColumns)) {
                $this->warn("\nMissing columns:");
                foreach ($missingColumns as $column) {
                    $this->line("- {$column}: {$expectedColumns[$column]}");
                }
            }

            if (! empty($extraColumns)) {
                $this->info("\nExtra columns (not in expected structure):");
                foreach ($extraColumns as $column) {
                    $this->line("- {$column}");
                }
            }

            if (empty($missingColumns) && empty($extraColumns)) {
                $this->info("\n✅ Brand table structure is complete!");
            }

            // Check sample data
            $brandCount = DB::connection($connection)->table('brands')->count();
            $this->info("\nTotal brands in database: {$brandCount}");

            if ($brandCount > 0) {
                $sampleBrands = DB::connection($connection)->table('brands')->limit(3)->get();
                $this->info("\nSample brand data:");
                foreach ($sampleBrands as $brand) {
                    $this->line("- ID: {$brand->id}, Name: {$brand->name}, Slug: ".($brand->slug ?? 'NULL'));
                }
            }

        } catch (\Exception $e) {
            $this->error('Error checking brand table: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
