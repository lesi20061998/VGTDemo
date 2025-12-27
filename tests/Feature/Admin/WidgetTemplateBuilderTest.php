<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\WidgetTemplateBuilder;
use App\Models\User;
use App\Models\WidgetTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WidgetTemplateBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_render_widget_template_builder(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetTemplateBuilder::class)
            ->assertStatus(200)
            ->assertSee('Tạo Widget Template');
    }

    public function test_can_create_widget_template(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetTemplateBuilder::class)
            ->set('name', 'Test Hero Banner')
            ->set('type', 'test_hero_banner')
            ->set('category', 'hero')
            ->set('description', 'A test hero banner widget')
            ->set('fields', [
                [
                    'name' => 'title',
                    'label' => 'Tiêu đề',
                    'type' => 'text',
                    'required' => true,
                    'default' => '',
                    'help' => 'Nhập tiêu đề chính',
                ],
                [
                    'name' => 'subtitle',
                    'label' => 'Mô tả',
                    'type' => 'textarea',
                    'required' => false,
                    'default' => '',
                    'help' => '',
                ],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('widget_templates', [
            'name' => 'Test Hero Banner',
            'type' => 'test_hero_banner',
            'category' => 'hero',
        ]);
    }

    public function test_can_add_field_to_template(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetTemplateBuilder::class)
            ->call('openAddFieldModal')
            ->assertSet('showFieldModal', true)
            ->set('currentField.name', 'hero_image')
            ->set('currentField.label', 'Hình ảnh Hero')
            ->set('currentField.type', 'image')
            ->set('currentField.required', true)
            ->call('saveField')
            ->assertSet('showFieldModal', false)
            ->assertCount('fields', 1);
    }

    public function test_can_edit_existing_template(): void
    {
        $this->actingAs($this->user);

        $template = WidgetTemplate::create([
            'name' => 'Existing Template',
            'type' => 'existing_template',
            'category' => 'general',
            'config_schema' => [
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
                ],
            ],
            'is_active' => true,
        ]);

        Livewire::test(WidgetTemplateBuilder::class, ['id' => $template->id])
            ->assertSet('name', 'Existing Template')
            ->assertSet('type', 'existing_template')
            ->assertCount('fields', 1);
    }

    public function test_validation_requires_name(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetTemplateBuilder::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_type_is_auto_generated_from_name(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetTemplateBuilder::class)
            ->set('name', 'Test Widget Name')
            ->assertSet('type', 'test_widget_name');
    }

    public function test_can_save_template_code(): void
    {
        $this->actingAs($this->user);

        $templateCode = '<div class="widget">{{ $settings["title"] ?? "Default" }}</div>';
        $templateCss = '.widget { padding: 1rem; }';
        $templateJs = 'console.log("Widget loaded");';

        Livewire::test(WidgetTemplateBuilder::class)
            ->set('name', 'Code Widget')
            ->set('category', 'general')
            ->set('template_code', $templateCode)
            ->set('template_css', $templateCss)
            ->set('template_js', $templateJs)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('widget_templates', [
            'name' => 'Code Widget',
            'type' => 'code_widget',
            'template_code' => $templateCode,
            'template_css' => $templateCss,
            'template_js' => $templateJs,
        ]);
    }

    public function test_template_render_method(): void
    {
        $this->actingAs($this->user);

        $template = WidgetTemplate::create([
            'name' => 'Render Test',
            'type' => 'render_test',
            'category' => 'general',
            'config_schema' => ['fields' => []],
            'template_code' => '<div class="test">{{ $settings["title"] ?? "No Title" }}</div>',
            'is_active' => true,
        ]);

        $html = $template->render(['title' => 'Hello World']);
        
        $this->assertStringContainsString('Hello World', $html);
        $this->assertStringContainsString('<div class="test">', $html);
    }
}
