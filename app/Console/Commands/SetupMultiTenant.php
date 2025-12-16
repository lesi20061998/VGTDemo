<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupMultiTenant extends Command
{
    protected $signature = 'setup:multi-tenant';
    protected $description = 'Setup multi-tenant architecture for project isolation';

    public function handle()
    {
        $this->info('Setting up multi-tenant architecture...');
        
        // Models that need project scoping (exclude global models like User, Employee, etc.)
        $projectScopedModels = [
            'Product',
            'ProductCategory', 
            'Brand',
            'ProductAttribute',
            'ProductAttributeValue',
            'ProductAttributeValueMapping',
            'ProductReview',
            'ProductVariation',
            'Order',
            'OrderItem',
            'OrderStatusHistory',
            'Menu',
            'MenuItem',
            'Widget',
            'Setting',
            'FormSubmission',
            'Branch',
            'Post',
            'Tag'
        ];
        
        foreach ($projectScopedModels as $model) {
            $this->addProjectScopedTrait($model);
        }
        
        $this->info('Multi-tenant setup completed!');
        $this->info('Each project will now have isolated data.');
        
        return 0;
    }
    
    private function addProjectScopedTrait($modelName)
    {
        $modelPath = app_path("Models/{$modelName}.php");
        
        if (!File::exists($modelPath)) {
            $this->warn("Model {$modelName} not found, skipping...");
            return;
        }
        
        $content = File::get($modelPath);
        
        // Check if ProjectScoped trait is already added
        if (strpos($content, 'ProjectScoped') !== false) {
            $this->info("Model {$modelName} already has ProjectScoped trait");
            return;
        }
        
        // Add use statement
        if (strpos($content, 'use App\\Traits\\ProjectScoped;') === false) {
            $content = str_replace(
                'use Illuminate\\Database\\Eloquent\\Model;',
                "use Illuminate\\Database\\Eloquent\\Model;\nuse App\\Traits\\ProjectScoped;",
                $content
            );
        }
        
        // Add trait to class
        $pattern = '/class\s+' . $modelName . '\s+extends\s+Model[^{]*\{[\s\n]*use\s+([^;]+);/';
        if (preg_match($pattern, $content, $matches)) {
            $existingTraits = $matches[1];
            $newTraits = $existingTraits . ', ProjectScoped';
            $content = str_replace(
                "use {$existingTraits};",
                "use {$newTraits};",
                $content
            );
        } else {
            // If no existing traits, add after class declaration
            $content = preg_replace(
                '/class\s+' . $modelName . '\s+extends\s+Model[^{]*\{/',
                "$0\n    use ProjectScoped;\n",
                $content
            );
        }
        
        File::put($modelPath, $content);
        $this->info("Added ProjectScoped trait to {$modelName}");
    }
}