<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Widgets\WidgetRegistry;
use App\Services\MetadataValidationService;

class WidgetValidateCommand extends Command
{
    protected $signature = 'widget:validate {type? : Widget type to validate}';
    protected $description = 'Validate widget metadata and configuration';

    public function handle(): int
    {
        $type = $this->argument('type');
        
        if ($type) {
            return $this->validateSingleWidget($type);
        }
        
        return $this->validateAllWidgets();
    }
    
    protected function validateSingleWidget(string $type): int
    {
        $this->info("Validating widget: {$type}");
        
        if (!WidgetRegistry::exists($type)) {
            $this->error("Widget type '{$type}' not found");
            return 1;
        }
        
        try {
            $config = WidgetRegistry::getConfig($type);
            $this->info('✓ Widget configuration loaded');
            
            // Validate metadata
            $validator = new MetadataValidationService();
            $validator->validateMetadata($config);
            $this->info('✓ Metadata validation passed');
            
            // Test widget creation
            $widgetClass = WidgetRegistry::get($type);
            $widget = new $widgetClass();
            $this->info('✓ Widget instantiation successful');
            
            // Test rendering
            $preview = $widget->getPreview();
            $this->info('✓ Widget preview generation successful');
            
            $this->info("Widget '{$type}' validation completed successfully!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Validation failed: " . $e->getMessage());
            return 1;
        }
    }
    
    protected function validateAllWidgets(): int
    {
        $this->info('Validating all widgets...');
        
        $widgets = WidgetRegistry::all();
        $passed = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($widgets as $widget) {
            $type = $widget['type'];
            
            try {
                // Validate metadata
                $validator = new MetadataValidationService();
                $validator->validateMetadata($widget['metadata']);
                
                // Test widget creation
                $widgetClass = $widget['class'];
                $testWidget = new $widgetClass();
                
                // Test rendering
                $testWidget->getPreview();
                
                $this->line("✓ {$type}");
                $passed++;
                
            } catch (\Exception $e) {
                $this->line("✗ {$type}: " . $e->getMessage());
                $errors[] = "{$type}: " . $e->getMessage();
                $failed++;
            }
        }
        
        $this->info("\nValidation Summary:");
        $this->info("Passed: {$passed}");
        if ($failed > 0) {
            $this->error("Failed: {$failed}");
            $this->line("\nErrors:");
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }
        
        return $failed > 0 ? 1 : 0;
    }
}