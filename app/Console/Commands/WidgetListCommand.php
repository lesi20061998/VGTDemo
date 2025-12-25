<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Widgets\WidgetRegistry;
use App\Models\Widget;

class WidgetListCommand extends Command
{
    protected $signature = 'widget:list {--category= : Filter by category} {--active : Show only active widgets} {--usage : Show widget usage statistics}';
    protected $description = 'List all registered widgets with their information';

    public function handle(): int
    {
        $category = $this->option('category');
        $activeOnly = $this->option('active');
        $showUsage = $this->option('usage');

        if ($showUsage) {
            return $this->showUsageStatistics();
        }

        $this->info('Registered Widgets:');
        $this->line('');

        try {
            $widgets = WidgetRegistry::all();
            
            if (empty($widgets)) {
                $this->warn('No widgets found. Run "php artisan widget:discover" to discover widgets.');
                return 0;
            }

            // Filter by category if specified
            if ($category) {
                $widgets = array_filter($widgets, function ($widget) use ($category) {
                    return ($widget['metadata']['category'] ?? 'general') === $category;
                });
                
                if (empty($widgets)) {
                    $this->warn("No widgets found in category '{$category}'");
                    return 0;
                }
            }

            // Group by category
            $byCategory = [];
            foreach ($widgets as $widget) {
                $cat = $widget['metadata']['category'] ?? 'general';
                $byCategory[$cat][] = $widget;
            }

            foreach ($byCategory as $cat => $categoryWidgets) {
                $this->line("<fg=cyan>Category: {$cat}</fg=cyan>");
                $this->line(str_repeat('-', 50));

                $tableData = [];
                foreach ($categoryWidgets as $widget) {
                    $metadata = $widget['metadata'];
                    
                    // Get usage count if active filter is used
                    $usageCount = '';
                    if ($activeOnly) {
                        $count = Widget::where('type', $widget['type'])->where('is_active', true)->count();
                        $usageCount = "({$count} active)";
                    }

                    $tableData[] = [
                        $widget['type'],
                        $metadata['name'] ?? 'N/A',
                        $metadata['version'] ?? 'N/A',
                        $metadata['author'] ?? 'N/A',
                        $widget['class'],
                        $usageCount
                    ];
                }

                $headers = ['Type', 'Name', 'Version', 'Author', 'Class'];
                if ($activeOnly) {
                    $headers[] = 'Usage';
                }

                $this->table($headers, $tableData);
                $this->line('');
            }

            $this->info('Total widgets: ' . count($widgets));
            
            // Show available categories
            $categories = array_keys($byCategory);
            $this->line('Available categories: ' . implode(', ', $categories));

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to list widgets: ' . $e->getMessage());
            return 1;
        }
    }

    protected function showUsageStatistics(): int
    {
        $this->info('Widget Usage Statistics:');
        $this->line('');

        try {
            // Get all widget types from database
            $usageStats = Widget::selectRaw('type, area, COUNT(*) as count, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count')
                ->groupBy('type', 'area')
                ->orderBy('count', 'desc')
                ->get();

            if ($usageStats->isEmpty()) {
                $this->warn('No widgets found in database.');
                return 0;
            }

            $tableData = [];
            $totalWidgets = 0;
            $totalActive = 0;

            foreach ($usageStats as $stat) {
                $totalWidgets += $stat->count;
                $totalActive += $stat->active_count;
                
                $tableData[] = [
                    $stat->type,
                    $stat->area,
                    $stat->count,
                    $stat->active_count,
                    $stat->count - $stat->active_count
                ];
            }

            $this->table(
                ['Widget Type', 'Area', 'Total', 'Active', 'Inactive'],
                $tableData
            );

            $this->line('');
            $this->info("Summary:");
            $this->line("Total widgets in database: {$totalWidgets}");
            $this->line("Active widgets: {$totalActive}");
            $this->line("Inactive widgets: " . ($totalWidgets - $totalActive));

            // Show area distribution
            $areaStats = Widget::selectRaw('area, COUNT(*) as count')
                ->groupBy('area')
                ->orderBy('count', 'desc')
                ->get();

            $this->line('');
            $this->info('Distribution by area:');
            foreach ($areaStats as $area) {
                $this->line("  {$area->area}: {$area->count} widgets");
            }

            // Show most used widget types
            $typeStats = Widget::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            $this->line('');
            $this->info('Top 10 most used widget types:');
            foreach ($typeStats as $type) {
                $this->line("  {$type->type}: {$type->count} instances");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to get usage statistics: ' . $e->getMessage());
            return 1;
        }
    }
}