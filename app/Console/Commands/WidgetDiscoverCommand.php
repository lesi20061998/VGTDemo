<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Widgets\WidgetRegistry;
use App\Services\WidgetDiscoveryService;

class WidgetDiscoverCommand extends Command
{
    protected $signature = 'widget:discover {--clear-cache : Clear widget discovery cache}';
    protected $description = 'Discover and register widgets automatically';

    public function handle(): int
    {
        if ($this->option('clear-cache')) {
            WidgetRegistry::clearCache();
            $this->info('Widget discovery cache cleared.');
        }

        $this->info('Discovering widgets...');
        
        try {
            $discovered = WidgetRegistry::discover();
            $this->info('Discovered ' . count($discovered) . ' widgets');
            
            // Show discovered widgets
            if (count($discovered) > 0) {
                $this->table(
                    ['Type', 'Class', 'Category', 'Name'],
                    collect($discovered)->map(function ($widget) {
                        return [
                            $widget['type'],
                            $widget['class'],
                            $widget['metadata']['category'] ?? 'N/A',
                            $widget['metadata']['name'] ?? 'N/A'
                        ];
                    })->toArray()
                );
            }
            
            // Check for conflicts
            $conflicts = WidgetRegistry::validateNamespaces();
            if (count($conflicts) > 0) {
                $this->warn('Found namespace conflicts:');
                foreach ($conflicts as $conflict) {
                    $this->error("Type '{$conflict['type']}' conflicts between: " . implode(', ', $conflict['classes']));
                }
            }
            
            // Show all widgets by category
            $byCategory = WidgetRegistry::getByCategory();
            $this->info("\nWidgets by category:");
            foreach ($byCategory as $category => $widgets) {
                $this->line("  {$category}: " . count($widgets) . ' widgets');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Widget discovery failed: ' . $e->getMessage());
            return 1;
        }
    }
}