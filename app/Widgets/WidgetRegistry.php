<?php

namespace App\Widgets;

use App\Contracts\WidgetRegistryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Widgets\Analytics\AnalyticsWidget;
use App\Widgets\Category\HomeCateWidget;
use App\Widgets\Custom\SimpleText\SimpleTextWidget;
use App\Widgets\Custom\TestWidget\TestWidgetWidget;
use App\Widgets\Hero\BentoGridHomeWidget;
use App\Widgets\Hero\FeaturesWidget;
use App\Widgets\Hero\HeroWidget;
use App\Widgets\Marketing\CtaWidget;
use App\Widgets\Marketing\NewsletterWidget;
use App\Widgets\Marketing\TestimonialWidget;
use App\Widgets\News\NewsArticleWidget;
use App\Widgets\News\NewsFeaturedWidget;
use App\Widgets\News\RelatedPostsWidget;
use App\Widgets\Post\PostListWidget;
use App\Widgets\Product\ProductCateWidget;
use App\Widgets\Product\ProductListWidget;
use App\Widgets\Product\ProductsWidget;
use App\Widgets\Slider\PostSliderWidget;

class WidgetRegistry implements WidgetRegistryInterface
{
    protected static array $widgets = [
        // Legacy manually registered widgets
        'hero' => HeroWidget::class,
        'features' => FeaturesWidget::class,
        'bento_grid_home' => BentoGridHomeWidget::class,
        'cta' => CtaWidget::class,
        'post_list' => PostListWidget::class,
        'post_slider' => PostSliderWidget::class,
        'newsletter' => NewsletterWidget::class,
        'testimonial' => TestimonialWidget::class,
        'product_list' => ProductListWidget::class,
        'products' => ProductsWidget::class,
        'product_cate' => ProductCateWidget::class,
        'home_cate' => HomeCateWidget::class,
        'news_article' => NewsArticleWidget::class,
        'news_featured' => NewsFeaturedWidget::class,
        'related_posts' => RelatedPostsWidget::class,
        'analytics' => AnalyticsWidget::class,
        'simple_text' => SimpleTextWidget::class,
        'test_widget' => TestWidgetWidget::class,
    ];

    protected static array $discoveredWidgets = [];
    protected static bool $discoveryComplete = false;

    /**
     * Automatically discover widgets in the widgets directory
     */
    public static function discover(): array
    {
        if (self::$discoveryComplete) {
            return self::$discoveredWidgets;
        }

        $cacheKey = 'widget_discovery_' . md5(app_path('Widgets'));
        
        self::$discoveredWidgets = Cache::remember($cacheKey, 3600, function () {
            return self::performDiscovery();
        });

        self::$discoveryComplete = true;
        return self::$discoveredWidgets;
    }

    /**
     * Perform the actual widget discovery
     */
    protected static function performDiscovery(): array
    {
        $discovered = [];
        $widgetsPath = app_path('Widgets');
        
        if (!File::isDirectory($widgetsPath)) {
            return $discovered;
        }

        $directories = File::directories($widgetsPath);
        
        foreach ($directories as $categoryDir) {
            $categoryName = basename($categoryDir);
            
            // Skip base files
            if (in_array($categoryName, ['BaseWidget.php', 'WidgetRegistry.php'])) {
                continue;
            }
            
            $widgetDirs = File::directories($categoryDir);
            
            foreach ($widgetDirs as $widgetDir) {
                $widgetName = basename($widgetDir);
                $widgetClass = self::buildWidgetClassName($categoryName, $widgetName);
                
                // Check if widget class exists
                if (!class_exists($widgetClass)) {
                    continue;
                }
                
                // Check if it extends BaseWidget
                if (!is_subclass_of($widgetClass, BaseWidget::class)) {
                    continue;
                }
                
                // Generate widget type from class name
                $widgetType = self::generateWidgetType($categoryName, $widgetName);
                
                // Validate metadata exists
                try {
                    $metadata = self::loadWidgetMetadata($widgetClass);
                    $discovered[$widgetType] = [
                        'class' => $widgetClass,
                        'type' => $widgetType,
                        'category' => $categoryName,
                        'name' => $widgetName,
                        'metadata' => $metadata,
                    ];
                } catch (\Exception $e) {
                    // Skip widgets with invalid metadata
                    \Log::warning("Skipping widget {$widgetClass}: " . $e->getMessage());
                    continue;
                }
            }
        }
        
        return $discovered;
    }

    /**
     * Build widget class name from category and widget name
     */
    protected static function buildWidgetClassName(string $category, string $widgetName): string
    {
        return "App\\Widgets\\{$category}\\{$widgetName}Widget";
    }

    /**
     * Generate widget type from category and name
     */
    protected static function generateWidgetType(string $category, string $widgetName): string
    {
        return Str::snake(Str::camel($category . '_' . $widgetName));
    }

    /**
     * Load widget metadata
     */
    protected static function loadWidgetMetadata(string $widgetClass): array
    {
        $metadataPath = $widgetClass::getMetadataPath();
        
        if (File::exists($metadataPath)) {
            $content = File::get($metadataPath);
            $metadata = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON in widget metadata: ' . json_last_error_msg());
            }
            
            return $metadata;
        }
        
        // Fallback to getConfig method
        if (method_exists($widgetClass, 'getConfig')) {
            return $widgetClass::getConfig();
        }
        
        throw new \RuntimeException('Widget metadata not found');
    }

    /**
     * Get all widgets (manual + discovered)
     */
    public static function all(): array
    {
        $discovered = self::discover();
        $allWidgets = [];
        
        // Add manually registered widgets
        foreach (self::$widgets as $type => $class) {
            try {
                $metadata = self::loadWidgetMetadata($class);
                $allWidgets[$type] = [
                    'type' => $type,
                    'class' => $class,
                    'metadata' => $metadata,
                ];
            } catch (\Exception $e) {
                \Log::warning("Error loading widget {$class}: " . $e->getMessage());
            }
        }
        
        // Add discovered widgets (they override manual ones if same type)
        foreach ($discovered as $type => $widget) {
            $allWidgets[$type] = $widget;
        }
        
        return array_values($allWidgets);
    }

    /**
     * Get widgets organized by category
     */
    public static function getByCategory(): array
    {
        $widgets = self::all();
        $categories = [];

        foreach ($widgets as $widget) {
            $category = $widget['metadata']['category'] ?? 'general';
            $categories[$category][] = $widget;
        }

        return $categories;
    }

    /**
     * Get widget class by type
     */
    public static function get(string $type): ?string
    {
        // Check manually registered first
        if (isset(self::$widgets[$type])) {
            return self::$widgets[$type];
        }
        
        // Check discovered widgets
        $discovered = self::discover();
        if (isset($discovered[$type])) {
            return $discovered[$type]['class'];
        }
        
        return null;
    }

    /**
     * Get widget configuration by type
     */
    public static function getConfig(string $type): ?array
    {
        $class = self::get($type);
        if (!$class) {
            return null;
        }

        try {
            $metadata = self::loadWidgetMetadata($class);
            $metadata['type'] = $type;
            $metadata['class'] = $class;
            return $metadata;
        } catch (\Exception $e) {
            \Log::error("Error loading config for widget {$type}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Render widget with settings and variant
     */
    public static function render(string $type, array $settings = [], string $variant = 'default'): string
    {
        $class = self::get($type);
        if (!$class) {
            return '';
        }

        try {
            $widget = new $class($settings, $variant);
            return $widget->css() . $widget->render() . $widget->js();
        } catch (\Exception $e) {
            \Log::error("Error rendering widget {$type}: " . $e->getMessage());
            return '<div class="widget-error">Widget rendering failed</div>';
        }
    }

    /**
     * Register a widget manually
     */
    public static function register(string $type, string $class): void
    {
        if (!is_subclass_of($class, BaseWidget::class)) {
            throw new \InvalidArgumentException("Widget class must extend BaseWidget");
        }
        
        self::$widgets[$type] = $class;
        
        // Clear discovery cache to include new widget
        Cache::forget('widget_discovery_' . md5(app_path('Widgets')));
        self::$discoveryComplete = false;
    }

    /**
     * Get all widget types
     */
    public static function getTypes(): array
    {
        $widgets = self::all();
        return array_column($widgets, 'type');
    }

    /**
     * Check if widget type exists
     */
    public static function exists(string $type): bool
    {
        return self::get($type) !== null;
    }

    /**
     * Get widget preview
     */
    public static function getPreview(string $type, array $settings = [], string $variant = 'default'): string
    {
        $class = self::get($type);
        if (!$class) {
            return '<div class="widget-error">Widget not found</div>';
        }

        try {
            $widget = new $class($settings, $variant);
            return $widget->getPreview();
        } catch (\Exception $e) {
            return '<div class="widget-error">Preview Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Clear discovery cache
     */
    public static function clearCache(): void
    {
        Cache::forget('widget_discovery_' . md5(app_path('Widgets')));
        self::$discoveryComplete = false;
        self::$discoveredWidgets = [];
    }

    /**
     * Validate widget namespace conflicts
     */
    public static function validateNamespaces(): array
    {
        $conflicts = [];
        $widgets = self::all();
        $types = [];
        
        foreach ($widgets as $widget) {
            $type = $widget['type'];
            if (isset($types[$type])) {
                $conflicts[] = [
                    'type' => $type,
                    'classes' => [$types[$type], $widget['class']]
                ];
            } else {
                $types[$type] = $widget['class'];
            }
        }
        
        return $conflicts;
    }
}
