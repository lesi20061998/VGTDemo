<?php

namespace Tests\Unit\Widgets;

use Tests\TestCase;
use App\Widgets\BaseWidget;
use App\Widgets\Hero\HeroWidget;
use Illuminate\Support\Facades\File;

class BaseWidgetTest extends TestCase
{
    public function test_widget_can_load_metadata_from_json_file(): void
    {
        // Create a test widget with metadata
        $settings = [
            'title' => 'Test Title',
            'subtitle' => 'Test Subtitle'
        ];
        
        $widget = new HeroWidget($settings);
        $metadata = $widget->getMetadata();
        
        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('name', $metadata);
        $this->assertArrayHasKey('fields', $metadata);
        $this->assertEquals('Hero Section', $metadata['name']);
    }

    public function test_widget_validates_settings_against_metadata(): void
    {
        $validSettings = [
            'title' => 'Valid Title',
            'subtitle' => 'Valid Subtitle',
            'button_text' => 'Click Me',
            'button_link' => 'https://example.com'
        ];
        
        $widget = new HeroWidget($validSettings);
        $this->assertTrue($widget->validateSettings());
    }

    public function test_widget_throws_exception_for_invalid_settings(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        // Missing required title field
        $invalidSettings = [
            'subtitle' => 'Valid Subtitle'
        ];
        
        new HeroWidget($invalidSettings);
    }

    public function test_widget_supports_variants(): void
    {
        $widget = new HeroWidget(['title' => 'Test'], 'compact');
        
        $this->assertEquals('compact', $widget->getVariant());
        
        $variants = $widget->getVariants();
        $this->assertIsArray($variants);
        $this->assertArrayHasKey('default', $variants);
        $this->assertArrayHasKey('compact', $variants);
    }

    public function test_widget_throws_exception_for_invalid_variant(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new HeroWidget(['title' => 'Test'], 'nonexistent');
    }

    public function test_widget_can_get_preview(): void
    {
        $widget = new HeroWidget(['title' => 'Test Title']);
        $preview = $widget->getPreview();
        
        $this->assertIsString($preview);
        $this->assertStringContainsString('Test Title', $preview);
    }

    public function test_widget_metadata_path_generation(): void
    {
        $path = HeroWidget::getMetadataPath();
        
        $this->assertStringEndsWith('widget.json', $path);
        $this->assertStringContainsString('Hero', $path);
    }
}