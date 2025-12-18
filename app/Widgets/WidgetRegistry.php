<?php

namespace App\Widgets;

use App\Widgets\Analytics\AnalyticsWidget;
use App\Widgets\Category\HomeCateWidget;
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

class WidgetRegistry
{
    protected static $widgets = [
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
    ];

    public static function all()
    {
        return array_map(function ($class, $type) {
            $config = $class::getConfig();
            $config['type'] = $type;
            $config['class'] = $class;

            return $config;
        }, self::$widgets, array_keys(self::$widgets));
    }

    public static function getByCategory()
    {
        $widgets = self::all();
        $categories = [];

        foreach ($widgets as $widget) {
            $category = $widget['category'] ?? 'general';
            $categories[$category][] = $widget;
        }

        return $categories;
    }

    public static function get($type)
    {
        return self::$widgets[$type] ?? null;
    }

    public static function getConfig($type)
    {
        $class = self::get($type);
        if (! $class) {
            return null;
        }

        $config = $class::getConfig();
        $config['type'] = $type;
        $config['class'] = $class;

        return $config;
    }

    public static function render($type, $settings)
    {
        $class = self::get($type);
        if (! $class) {
            return '';
        }

        $widget = new $class($settings);

        return $widget->css().$widget->render().$widget->js();
    }

    public static function register($type, $class)
    {
        self::$widgets[$type] = $class;
    }

    public static function getTypes()
    {
        return array_keys(self::$widgets);
    }
}
