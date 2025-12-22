<?php

namespace App\Console\Commands;

use App\Models\ProjectBrand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugBrandQuery extends Command
{
    protected $signature = 'debug:brands';

    protected $description = 'Debug brand query issues';

    public function handle(): int
    {
        $this->info('Debugging brand queries...');

        try {
            // Test basic query
            $this->info("\n=== Testing basic ProjectBrand query ===");
            $brands = ProjectBrand::all();
            $this->line('Total brands found: '.$brands->count());

            if ($brands->count() > 0) {
                $firstBrand = $brands->first();
                $this->line('First brand type: '.gettype($firstBrand));
                $this->line('First brand class: '.get_class($firstBrand));
                $this->line('First brand ID: '.($firstBrand->id ?? 'NULL'));
                $this->line('First brand name: '.($firstBrand->name ?? 'NULL'));
            }

            // Test pagination
            $this->info("\n=== Testing pagination ===");
            $paginatedBrands = ProjectBrand::paginate(20);
            $this->line('Paginated brands type: '.gettype($paginatedBrands));
            $this->line('Paginated brands class: '.get_class($paginatedBrands));
            $this->line('Items count: '.$paginatedBrands->count());

            if ($paginatedBrands->count() > 0) {
                $firstItem = $paginatedBrands->first();
                $this->line('First item type: '.gettype($firstItem));
                if (is_object($firstItem)) {
                    $this->line('First item class: '.get_class($firstItem));
                    $this->line('First item ID: '.($firstItem->id ?? 'NULL'));
                }
            }

            // Test with search
            $this->info("\n=== Testing with search ===");
            $searchBrands = ProjectBrand::when('test', fn ($q) => $q->where('name', 'like', '%test%'))
                ->orderBy('name')
                ->paginate(20);

            $this->line('Search brands count: '.$searchBrands->count());

            // Test database connection
            $this->info("\n=== Testing database connection ===");
            $connection = ProjectBrand::getConnectionName();
            $this->line('Connection name: '.($connection ?? 'default'));

            $dbName = DB::connection($connection)->select('SELECT DATABASE() as db_name')[0]->db_name;
            $this->line('Database name: '.$dbName);

            // Test raw query
            $this->info("\n=== Testing raw query ===");
            $rawBrands = DB::connection('project')->table('brands')->limit(3)->get();
            $this->line('Raw query count: '.$rawBrands->count());

            foreach ($rawBrands as $brand) {
                $this->line("Raw brand: ID={$brand->id}, Name={$brand->name}, Type=".gettype($brand));
            }

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
            $this->error('File: '.$e->getFile());
            $this->error('Line: '.$e->getLine());

            return 1;
        }

        return 0;
    }
}
