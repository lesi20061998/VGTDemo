<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMetaFieldsToHd001 extends Command
{
    protected $signature = 'add:meta-hd001';

    protected $description = 'Add meta_title and meta_description to project_hd001.brands table';

    public function handle(): int
    {
        $this->info('Adding meta fields to project_hd001.brands table...');

        try {
            // Add meta_title column
            $this->info('Adding meta_title column...');
            DB::statement('
                ALTER TABLE project_hd001.brands 
                ADD COLUMN meta_title VARCHAR(255) NULL 
                AFTER is_active
            ');
            $this->info('✅ meta_title column added');

            // Add meta_description column
            $this->info('Adding meta_description column...');
            DB::statement('
                ALTER TABLE project_hd001.brands 
                ADD COLUMN meta_description TEXT NULL 
                AFTER meta_title
            ');
            $this->info('✅ meta_description column added');

            // Verify the columns were added
            $this->info("\n=== Verifying new structure ===");
            $columns = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'project_hd001' 
                AND TABLE_NAME = 'brands' 
                ORDER BY ORDINAL_POSITION
            ");

            foreach ($columns as $column) {
                $nullable = $column->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
                if (in_array($column->COLUMN_NAME, ['meta_title', 'meta_description'])) {
                    $this->line("✅ {$column->COLUMN_NAME} ({$column->DATA_TYPE}) {$nullable}");
                } else {
                    $this->line("- {$column->COLUMN_NAME} ({$column->DATA_TYPE}) {$nullable}");
                }
            }

            $this->info("\n✅ Meta fields successfully added to project_hd001.brands!");
            $this->info('You can now use meta_title and meta_description in your brand forms.');

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());

            // Check if columns already exist
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                $this->warn('⚠️  Columns may already exist. Let me check...');

                $existingColumns = DB::select("
                    SELECT COLUMN_NAME 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = 'project_hd001' 
                    AND TABLE_NAME = 'brands' 
                    AND COLUMN_NAME IN ('meta_title', 'meta_description')
                ");

                if (count($existingColumns) > 0) {
                    $this->info('✅ Meta columns already exist:');
                    foreach ($existingColumns as $col) {
                        $this->line("- {$col->COLUMN_NAME}");
                    }

                    return 0;
                }
            }

            return 1;
        }

        return 0;
    }
}
