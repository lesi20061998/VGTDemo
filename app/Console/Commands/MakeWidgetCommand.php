<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeWidgetCommand extends Command
{
    protected $signature = 'make:widget {name} {--category=general} {--force}';
    protected $description = 'Create a new widget with metadata and class files';

    public function handle(): int
    {
        $name = $this->argument('name');
        $category = $this->option('category');
        $force = $this->option('force');

        // Validate name
        if (!preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name)) {
            $this->error('Widget name must be in PascalCase (e.g., HeroSection, ContactForm)');
            return 1;
        }

        $widgetName = $name . 'Widget';
        $categoryDir = Str::studly($category);
        $widgetDir = app_path("Widgets/{$categoryDir}/{$name}");
        $widgetClass = app_path("Widgets/{$categoryDir}/{$name}/{$widgetName}.php");
        $metadataFile = app_path("Widgets/{$categoryDir}/{$name}/widget.json");

        // Check if widget already exists
        if (File::exists($widgetClass) && !$force) {
            $this->error("Widget {$widgetName} already exists in {$categoryDir} category!");
            $this->info("Use --force to overwrite existing widget");
            return 1;
        }

        // Create directory
        if (!File::isDirectory($widgetDir)) {
            File::makeDirectory($widgetDir, 0755, true);
            $this->info("Created directory: {$widgetDir}");
        }

        // Create widget class
        $this->createWidgetClass($widgetClass, $widgetName, $categoryDir, $name);
        $this->info("Created widget class: {$widgetClass}");

        // Create metadata file
        $this->createMetadataFile($metadataFile, $name, $category);
        $this->info("Created metadata file: {$metadataFile}");

        // Create view directory
        $viewDir = $widgetDir . '/views';
        if (!File::isDirectory($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
            $this->createDefaultView($viewDir . '/default.blade.php', $name);
            $this->info("Created default view: {$viewDir}/default.blade.php");
        }

        // Create assets directory
        $assetsDir = $widgetDir . '/assets';
        if (!File::isDirectory($assetsDir)) {
            File::makeDirectory($assetsDir, 0755, true);
            $this->createAssetFiles($assetsDir, $name);
            $this->info("Created assets directory: {$assetsDir}");
        }

        $this->info("\n✅ Widget '{$widgetName}' created successfully!");
        $this->info("📁 Location: {$widgetDir}");
        $this->info("🔧 Run 'php artisan widget:discover' to register the new widget");
        
        return 0;
    }

    protected function createWidgetClass(string $filePath, string $widgetName, string $category, string $name): void
    {
        $namespace = "App\\Widgets\\{$category}";
        $className = $widgetName;
        $lowerName = Str::snake($name);

        $content = "<?php

namespace {$namespace};

use App\\Widgets\\BaseWidget;

class {$className} extends BaseWidget
{
    public function render(): string
    {
        \$title = \$this->get('title', 'Default Title');
        \$description = \$this->get('description', 'Default description for {$name} widget');
        \$isActive = \$this->get('is_active', true);
        
        if (!\$isActive) {
            return '';
        }
        
        return \"
        <section class=\\\"{$lowerName}-widget py-8\\\">
            <div class=\\\"container mx-auto px-4\\\">
                <h2 class=\\\"text-2xl font-bold mb-4\\\">{\$title}</h2>
                <p class=\\\"text-gray-600\\\">{\$description}</p>
            </div>
        </section>\";
    }

    public function css(): string
    {
        return '<style>
        .{$lowerName}-widget {
            /* Add your custom CSS here */
        }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        // Add your custom JavaScript here
        console.log(\"{$className} loaded\");
        </script>';
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        return [
            'name' => '{$name}',
            'description' => 'A customizable {$name} widget',
            'category' => '{$category}',
            'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 6h16M4 12h16M4 18h16\"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Default Title'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'default' => 'Default description'],
                ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => true],
            ]
        ];
    }
}
";

        File::put($filePath, $content);
    }

    protected function createMetadataFile(string $filePath, string $name, string $category): void
    {
        $lowerName = Str::snake($name);
        
        $metadata = [
            'name' => $name,
            'description' => "A customizable {$name} widget for your website",
            'category' => $category,
            'version' => '1.0.0',
            'author' => 'Development Team',
            'icon' => 'heroicon-outline-cube',
            'preview_image' => 'preview.jpg',
            'variants' => [
                'default' => 'Default Layout',
                'compact' => 'Compact Layout'
            ],
            'fields' => [
                [
                    'name' => 'title',
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true,
                    'default' => 'Default Title',
                    'validation' => 'required|string|max:100',
                    'help' => 'The main title for the widget'
                ],
                [
                    'name' => 'description',
                    'label' => 'Description',
                    'type' => 'textarea',
                    'required' => false,
                    'default' => 'Default description',
                    'validation' => 'string|max:500',
                    'help' => 'Optional description text',
                    'rows' => 3
                ],
                [
                    'name' => 'is_active',
                    'label' => 'Active',
                    'type' => 'checkbox',
                    'required' => false,
                    'default' => true,
                    'help' => 'Enable or disable this widget'
                ],
                [
                    'name' => 'background_color',
                    'label' => 'Background Color',
                    'type' => 'color',
                    'required' => false,
                    'default' => '#ffffff',
                    'help' => 'Background color for the widget'
                ]
            ],
            'settings' => [
                'cacheable' => true,
                'cache_duration' => 3600,
                'permissions' => ['admin', 'editor'],
                'dependencies' => []
            ]
        ];

        File::put($filePath, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function createDefaultView(string $filePath, string $name): void
    {
        $lowerName = Str::snake($name);
        
        $content = "{{-- Default view for {$name} Widget --}}
<section class=\"{$lowerName}-widget py-8\">
    <div class=\"container mx-auto px-4\">
        <h2 class=\"text-2xl font-bold mb-4\">{{ \$title ?? 'Default Title' }}</h2>
        <p class=\"text-gray-600\">{{ \$description ?? 'Default description' }}</p>
        
        {{-- Add your custom HTML here --}}
        <div class=\"mt-6\">
            <p class=\"text-sm text-gray-500\">This is the default view for {$name} widget.</p>
        </div>
    </div>
</section>
";

        File::put($filePath, $content);
    }

    protected function createAssetFiles(string $assetsDir, string $name): void
    {
        $lowerName = Str::snake($name);
        
        // Create CSS file
        $cssContent = "/* Styles for {$name} Widget */
.{$lowerName}-widget {
    /* Add your custom styles here */
}

.{$lowerName}-widget h2 {
    /* Title styles */
}

.{$lowerName}-widget p {
    /* Paragraph styles */
}
";
        File::put($assetsDir . '/styles.css', $cssContent);

        // Create JS file
        $jsContent = "// JavaScript for {$name} Widget
document.addEventListener('DOMContentLoaded', function() {
    console.log('{$name} Widget loaded');
    
    // Add your custom JavaScript here
    const widgets = document.querySelectorAll('.{$lowerName}-widget');
    
    widgets.forEach(widget => {
        // Initialize widget functionality
        console.log('Initializing {$name} widget:', widget);
    });
});
";
        File::put($assetsDir . '/scripts.js', $jsContent);
    }
}