<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearCacheCommand extends Command
{
    protected $signature = 'cache:clear-app {--type=all}';
    protected $description = 'Clear application cache';

    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'dashboard':
                CacheService::forget(CacheService::DASHBOARD_STATS);
                CacheService::forget(CacheService::VISITOR_STATS);
                CacheService::forget(CacheService::TOP_IPS);
                $this->info('Dashboard cache cleared');
                break;
            case 'products':
                CacheService::forget(CacheService::PRODUCTS_LIST);
                $this->info('Products cache cleared');
                break;
            case 'all':
            default:
                CacheService::flush();
                $this->info('All cache cleared');
                break;
        }
    }
}