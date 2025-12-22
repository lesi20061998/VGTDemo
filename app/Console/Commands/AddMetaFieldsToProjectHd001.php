<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMetaFieldsToProjectHd001 extends Command
{
    protected $signature = 'add:meta-fields-hd001';

    protected $description = 'Add meta_title and meta_description columns to project_hd001.brands table';

    public function handle(): int
    {
        $this->info('Adding meta fields to project_hd001.brands table...');

        try {
            // First check if project_hd001 database exists
            $databases = DB::select("SHOW DATABASES LIKE 'project_hd001'");
            if (empty($databases)) {
                $this->error('❌ Database project_hd001 does not exist!');

                return 1;
            }

            $this->info('✅ Database project_hd001 found');

            // Check if brands table exists in project_hd001
            $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'project_hd001' AND TABLE_NAME = 'brands'");
            if (empty($tables)) {
                $this->error('❌ Table brands does not exist in project_hd001!');

                return 1;
            }

            $this->info('✅ Table brands found in project_hd001');

            // Check if meta columns already exist
            $existingColumns = DB::select("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'project_hd001' 
                AND TABLE_NAME = 'brands' 
                AND COLUMN_NAME IN ('meta_title', 'meta_description')
            ");

            $existingColumnNames = collect($existingColumns)->pluck('COLUMN_NAME')->toArray();

            if (in_array('meta_title', $existingColumnNames) && in_array('meta_description', $existingColumnNames)) {
                $this->info('✅ Meta columns already exist in project_hd001.brands');

                return 0;
            }

            // Add meta_title column if it doesn't exist
            if (! in_array('meta_title', $existingColumnNames)) {
                $this->info('Adding meta_title column...');
                DB::statement('
                    ALTER TABLE project_hd001.brands 
                    ADD COLUMN meta_title VARCHAR(255) NULL 
                    AFTER is_active
                ');
                $this->info('✅ meta_title column added');
            } else {
                $this->info('✅ meta_title column already exists');
            }

            // Add meta_description column if it doesn't exist
            if (! in_array('meta_description', $existingColumnNames)) {
                $this->info('Adding meta_description column...');
                DB::statement('
                    ALTER TABLE project_hd001.brands 
                    ADD COLUMN meta_description TEXT NULL 
                    AFTER meta_title
                ');
                $this->info('✅ meta_description column added');
            } else {
                $this->info('✅ meta_description column already exists');
            }

            // Verify the columns were added
            $this->info("\n=== Verifying columns in project_hd001.brands ===");
            $allColumns = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'project_hd001' 
                AND TABLE_NAME = 'brands' 
                ORDER BY ORDINAL_POSITION
            ");

            foreach ($allColumns as $column) {
                $nullable = $column->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
                $this->line("- {$column->COLUMN_NAME} ({$column->DATA_TYPE}) {$nullable}");
            }

            // Show sample data
            $this->info("\n=== Sample data from project_hd001.brands ===");
            $sampleData = DB::select('
                SELECT id, name, meta_title, meta_description 
                FROM project_hd001.brands 
                LIMIT 3
            ');

            foreach ($sampleData as $brand) {
                $this->line(sprintf(
                    'ID: %d | Name: %s | Meta Title: %s | Meta Desc: %s',
                    $brand->id,
                    $brand->name,
                    $brand->meta_title ?? 'NULL',
                    $brand->meta_description ?? 'NULL'
                ));
            }

            $this->info("\n✅ Meta fields successfully added to project_hd001.brands!");

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
