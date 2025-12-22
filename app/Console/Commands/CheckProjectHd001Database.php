<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckProjectHd001Database extends Command
{
    protected $signature = 'check:hd001-db';

    protected $description = 'Check if project_hd001 database exists and its structure';

    public function handle(): int
    {
        $this->info('Checking project_hd001 database...');

        try {
            // List all databases
            $this->info("\n=== All Available Databases ===");
            $databases = DB::select('SHOW DATABASES');
            foreach ($databases as $db) {
                $dbName = $db->Database;
                if (strpos($dbName, 'hd001') !== false || strpos($dbName, 'project') !== false) {
                    $this->line("🔍 {$dbName} (potential match)");
                } else {
                    $this->line("- {$dbName}");
                }
            }

            // Check specifically for project_hd001
            $this->info("\n=== Checking for project_hd001 ===");
            $hd001Exists = DB::select("SHOW DATABASES LIKE 'project_hd001'");

            if (empty($hd001Exists)) {
                $this->error("❌ Database 'project_hd001' does NOT exist");

                // Check for similar names
                $this->info("\n=== Looking for similar database names ===");
                $similarDbs = DB::select("SHOW DATABASES LIKE '%hd001%'");
                if (! empty($similarDbs)) {
                    foreach ($similarDbs as $db) {
                        $this->line("Found similar: {$db->Database}");
                    }
                } else {
                    $this->line("No databases containing 'hd001' found");
                }

                return 1;
            }

            $this->info("✅ Database 'project_hd001' exists!");

            // Check tables in project_hd001
            $this->info("\n=== Tables in project_hd001 ===");
            $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'project_hd001'");

            $brandsTableExists = false;
            foreach ($tables as $table) {
                if ($table->TABLE_NAME === 'brands') {
                    $this->line("✅ {$table->TABLE_NAME} (target table)");
                    $brandsTableExists = true;
                } else {
                    $this->line("- {$table->TABLE_NAME}");
                }
            }

            if (! $brandsTableExists) {
                $this->error("❌ 'brands' table does NOT exist in project_hd001");

                return 1;
            }

            // Check brands table structure
            $this->info("\n=== Brands table structure in project_hd001 ===");
            $columns = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'project_hd001' 
                AND TABLE_NAME = 'brands' 
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
                $this->warn("\n⚠️  Meta fields are missing. Need to add them to project_hd001.brands table.");
            } else {
                $this->info("\n✅ All meta fields exist in project_hd001.brands table!");
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
