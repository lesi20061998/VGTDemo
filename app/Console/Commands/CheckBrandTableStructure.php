<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckBrandTableStructure extends Command
{
    protected $signature = 'check:brand-table {project?}';

    protected $description = 'Check brand table structure for a specific project';

    public function handle(): int
    {
        $project = $this->argument('project') ?? 'hd001';

        $this->info("Checking brand table structure for project: {$project}");

        try {
            // Switch to project database
            config(['database.connections.project.database' => "project_{$project}"]);
            DB::purge('project');

            // Check if brands table exists
            if (! Schema::connection('project')->hasTable('brands')) {
                $this->error("Table 'brands' does not exist in project_{$project} database!");

                return 1;
            }

            // Get table structure
            $columns = DB::connection('project')->select('DESCRIBE brands');

            $this->info("\n=== BRANDS TABLE STRUCTURE ===");
            $this->table(
                ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'],
                collect($columns)->map(function ($column) {
                    return [
                        $column->Field,
                        $column->Type,
                        $column->Null,
                        $column->Key,
                        $column->Default ?? 'NULL',
                        $column->Extra,
                    ];
                })->toArray()
            );

            // Check expected columns
            $expectedColumns = [
                'id', 'name', 'slug', 'description', 'logo', 'is_active',
                'meta_title', 'meta_description', 'created_at', 'updated_at',
            ];

            $actualColumns = collect($columns)->pluck('Field')->toArray();

            $this->info("\n=== COLUMN CHECK ===");
            foreach ($expectedColumns as $column) {
                if (in_array($column, $actualColumns)) {
                    $this->info("✓ {$column} - EXISTS");
                } else {
                    $this->error("✗ {$column} - MISSING");
                }
            }

            // Check for unexpected columns
            $unexpectedColumns = array_diff($actualColumns, $expectedColumns);
            if (! empty($unexpectedColumns)) {
                $this->warn("\n=== UNEXPECTED COLUMNS ===");
                foreach ($unexpectedColumns as $column) {
                    $this->warn("! {$column} - UNEXPECTED");
                }
            }

            // Count records
            $count = DB::connection('project')->table('brands')->count();
            $this->info("\nTotal brands: {$count}");

            // Show sample data if exists
            if ($count > 0) {
                $samples = DB::connection('project')->table('brands')->limit(3)->get();
                $this->info("\n=== SAMPLE DATA ===");
                foreach ($samples as $sample) {
                    $this->line("ID: {$sample->id} | Name: {$sample->name} | Slug: {$sample->slug}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");

            return 1;
        }
    }
}
