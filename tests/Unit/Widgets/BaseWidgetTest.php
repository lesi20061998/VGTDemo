<?php

namespace Tests\Unit\Widgets;

use App\Widgets\Hero\HeroWidget;
use Tests\TestCase;

class BaseWidgetTest extends TestCase
{
    public function test_widget_can_load_metadata_from_json_file(): void
    {
        // Create a test widget with metadata
        $settings = [
            'title' => 'Test Title',
            'subtitle' => 'Test Subtitle',
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
            'button_link' => 'https://example.com',
        ];

        $widget = new HeroWidget($validSettings);
        $this->assertTrue($widget->validateSettings());
    }

    public function test_widget_throws_exception_for_invalid_settings(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Missing required title field
        $invalidSettings = [
            'subtitle' => 'Valid Subtitle',
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

    public function test_widget_does_not_render_hardcoded_background_color_when_empty(): void
    {
        $widget = new HeroWidget([
            'title' => 'Test Title',
            'bg_color' => '',
            'background_image' => '',
        ]);

        $html = $widget->render();

        $this->assertStringNotContainsString('style="background-color: #3b82f6;"', $html);
        $this->assertStringNotContainsString('style=', $html);
    }

    public function test_widget_renders_background_image_correctly(): void
    {
        $widget = new HeroWidget([
            'title' => 'Test Title',
            'bg_type' => 'image',
            'background_image' => '/uploads/banner.jpg',
        ]);

        $html = $widget->render();

        $this->assertStringContainsString("background-image: url('/uploads/banner.jpg')", $html);
    }

    public function test_widget_renders_custom_bg_color_and_tailwind_class(): void
    {
        $hexWidget = new HeroWidget([
            'title' => 'Test Title',
            'bg_color' => '#ff0000',
        ]);
        $this->assertStringContainsString('background-color: #ff0000', $hexWidget->render());

        $tailwindWidget = new HeroWidget([
            'title' => 'Test Title',
            'bg_color' => 'bg-blue-600',
        ]);
        $this->assertStringContainsString('class="hero-section text-white py-20 bg-blue-600"', $tailwindWidget->render());
    }
}
