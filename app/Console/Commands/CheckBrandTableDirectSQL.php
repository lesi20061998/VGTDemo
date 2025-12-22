<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckBrandTableDirectSQL extends Command
{
    protected $signature = 'check:brand-table-sql';

    protected $description = 'Check brand table structure using direct SQL queries';

    public function handle(): int
    {
        $this->info('Checking brand table structure using direct SQL...');

        try {
            // Check table structure with SHOW COLUMNS
            $this->info("\n=== SHOW COLUMNS FROM brands ===");
            $columns = DB::connection('project')->select(query: 'SHOW COLUMNS FROM brands');

            foreach ($columns as $column) {
                $this->line(sprintf(
                    '%-20s %-20s %-5s %-5s %-10s %s',
                    $column->Field,
                    $column->Type,
                    $column->Null,
                    $column->Key ?? '',
                    $column->Default ?? 'NULL',
                    $column->Extra ?? ''
                ));
            }

            // Check if meta columns exist specifically
            $this->info("\n=== Checking for meta columns ===");
            $metaTitleExists = collect($columns)->where('Field', 'meta_title')->isNotEmpty();
            $metaDescExists = collect($columns)->where('Field', 'meta_description')->isNotEmpty();

            $this->line('meta_title exists: '.($metaTitleExists ? '✅ YES' : '❌ NO'));
            $this->line('meta_description exists: '.($metaDescExists ? '✅ YES' : '❌ NO'));

            // Show sample data with meta fields
            $this->info("\n=== Sample data with meta fields ===");
            $sampleData = DB::connection('project')
                ->table('brands')
                ->select('id', 'name', 'meta_title', 'meta_description')
                ->limit(3)
                ->get();

            foreach ($sampleData as $brand) {
                $this->line(sprintf(
                    'ID: %d | Name: %s | Meta Title: %s | Meta Desc: %s',
                    $brand->id,
                    $brand->name,
                    $brand->meta_title ?? 'NULL',
                    $brand->meta_description ?? 'NULL'
                ));
            }

            // Check database name to confirm we're looking at the right one
            $this->info("\n=== Database Information ===");
            $dbName = DB::connection('project')->select('SELECT DATABASE() as db_name')[0]->db_name;
            $this->line("Current database: {$dbName}");

            // Show table creation statement
            $this->info("\n=== Table Creation Statement ===");
            $createTable = DB::connection('project')->select('SHOW CREATE TABLE brands')[0];
            $this->line($createTable->{'Create Table'});

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
