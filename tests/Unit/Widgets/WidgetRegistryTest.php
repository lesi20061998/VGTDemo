<?php

namespace Tests\Unit\Widgets;

use Tests\TestCase;
use App\Widgets\WidgetRegistry;
use App\Widgets\Hero\HeroWidget;
use Illuminate\Support\Facades\Cache;

class WidgetRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear widget discovery cache
        WidgetRegistry::clearCache();
    }

    public function test_registry_can_discover_widgets(): void
    {
        $discovered = WidgetRegistry::discover();
        
        $this->assertIsArray($discovered);
    }

    public function test_registry_can_get_all_widgets(): void
    {
        $widgets = WidgetRegistry::all();
        
        $this->assertIsArray($widgets);
        $this->assertNotEmpty($widgets);
        
        // Check that each widget has required properties
        foreach ($widgets as $widget) {
            $this->assertArrayHasKey('type', $widget);
            $this->assertArrayHasKey('class', $widget);
            $this->assertArrayHasKey('metadata', $widget);
        }
    }

    public function test_registry_can_organize_widgets_by_category(): void
    {
        $byCategory = WidgetRegistry::getByCategory();
        
        $this->assertIsArray($byCategory);
        
        // Should have at least the hero category
        $this->assertArrayHasKey('hero', $byCategory);
    }

    public function test_registry_can_get_widget_by_type(): void
    {
        $heroClass = WidgetRegistry::get('hero');
        
        $this->assertEquals(HeroWidget::class, $heroClass);
    }

    public function test_registry_returns_null_for_nonexistent_widget(): void
    {
        $nonexistent = WidgetRegistry::get('nonexistent_widget');
        
        $this->assertNull($nonexistent);
    }

    public function test_registry_can_render_widget(): void
    {
        $output = WidgetRegistry::render('hero', [
            'title' => 'Test Title',
            'subtitle' => 'Test Subtitle'
        ]);
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Test Title', $output);
    }

    public function test_registry_can_register_new_widget(): void
    {
        $testClass = HeroWidget::class;
        
        WidgetRegistry::register('test_widget', $testClass);
        
        $this->assertEquals($testClass, WidgetRegistry::get('test_widget'));
    }

    public function test_registry_validates_widget_class_on_registration(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        WidgetRegistry::register('invalid_widget', \stdClass::class);
    }

    public function test_registry_can_check_widget_existence(): void
    {
        $this->assertTrue(WidgetRegistry::exists('hero'));
        $this->assertFalse(WidgetRegistry::exists('nonexistent'));
    }

    public function test_registry_can_get_widget_types(): void
    {
        $types = WidgetRegistry::getTypes();
        
        $this->assertIsArray($types);
        $this->assertContains('hero', $types);
    }

    public function test_registry_can_get_widget_preview(): void
    {
        $preview = WidgetRegistry::getPreview('hero', [
            'title' => 'Preview Title'
        ]);
        
        $this->assertIsString($preview);
        $this->assertStringContainsString('Preview Title', $preview);
    }
}