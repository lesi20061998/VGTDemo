<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WidgetTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WidgetTemplateExportImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_export_single_widget_template(): void
    {
        $template = WidgetTemplate::create([
            'name' => 'Test Widget',
            'type' => 'test_widget',
            'category' => 'general',
            'description' => 'Test description',
            'icon' => 'cube',
            'config_schema' => ['fields' => [['name' => 'title', 'type' => 'text', 'label' => 'Title']]],
            'default_settings' => ['title' => 'Default'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('cms.widget-templates.export', $template->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        
        $data = $response->json();
        $this->assertEquals('1.0', $data['version']);
        $this->assertEquals('Test Widget', $data['template']['name']);
        $this->assertEquals('test_widget', $data['template']['type']);
    }

    public function test_can_export_all_widget_templates(): void
    {
        WidgetTemplate::create([
            'name' => 'Widget 1',
            'type' => 'widget_1',
            'category' => 'general',
            'config_schema' => ['fields' => []],
            'is_active' => true,
        ]);

        WidgetTemplate::create([
            'name' => 'Widget 2',
            'type' => 'widget_2',
            'category' => 'hero',
            'config_schema' => ['fields' => []],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('cms.widget-templates.export-all'));

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertEquals(2, $data['count']);
        $this->assertCount(2, $data['templates']);
    }

    public function test_can_import_single_widget_template(): void
    {
        $jsonContent = json_encode([
            'version' => '1.0',
            'template' => [
                'name' => 'Imported Widget',
                'type' => 'imported_widget',
                'category' => 'general',
                'config_schema' => ['fields' => [['name' => 'title', 'type' => 'text', 'label' => 'Title']]],
                'is_active' => true,
            ],
        ]);

        $file = UploadedFile::fake()->createWithContent('widget.json', $jsonContent);

        $response = $this->actingAs($this->user)
            ->post(route('cms.widget-templates.import'), [
                'json_file' => $file,
            ]);

        $response->assertRedirect(route('cms.widget-templates.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('widget_templates', [
            'name' => 'Imported Widget',
            'type' => 'imported_widget',
        ]);
    }

    public function test_can_import_multiple_widget_templates(): void
    {
        $jsonContent = json_encode([
            'version' => '1.0',
            'count' => 2,
            'templates' => [
                [
                    'name' => 'Widget A',
                    'type' => 'widget_a',
                    'category' => 'general',
                    'config_schema' => ['fields' => []],
                    'is_active' => true,
                ],
                [
                    'name' => 'Widget B',
                    'type' => 'widget_b',
                    'category' => 'hero',
                    'config_schema' => ['fields' => []],
                    'is_active' => true,
                ],
            ],
        ]);

        $file = UploadedFile::fake()->createWithContent('widgets.json', $jsonContent);

        $response = $this->actingAs($this->user)
            ->post(route('cms.widget-templates.import'), [
                'json_file' => $file,
            ]);

        $response->assertRedirect(route('cms.widget-templates.index'));

        $this->assertDatabaseHas('widget_templates', ['type' => 'widget_a']);
        $this->assertDatabaseHas('widget_templates', ['type' => 'widget_b']);
    }

    public function test_import_skips_duplicate_templates(): void
    {
        // Create existing template
        WidgetTemplate::create([
            'name' => 'Existing Widget',
            'type' => 'existing_widget',
            'category' => 'general',
            'config_schema' => ['fields' => []],
            'is_active' => true,
        ]);

        $jsonContent = json_encode([
            'version' => '1.0',
            'template' => [
                'name' => 'Duplicate Widget',
                'type' => 'existing_widget',
                'category' => 'general',
                'config_schema' => ['fields' => []],
                'is_active' => true,
            ],
        ]);

        $file = UploadedFile::fake()->createWithContent('widget.json', $jsonContent);

        $response = $this->actingAs($this->user)
            ->post(route('cms.widget-templates.import'), [
                'json_file' => $file,
            ]);

        $response->assertRedirect();
        
        // Should still have only one template with this type
        $this->assertEquals(1, WidgetTemplate::where('type', 'existing_widget')->count());
    }

    public function test_import_validates_json_file_required(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('cms.widget-templates.import'), []);

        $response->assertSessionHasErrors('json_file');
    }

    public function test_import_rejects_invalid_json(): void
    {
        $file = UploadedFile::fake()->createWithContent('invalid.json', 'not valid json');

        $response = $this->actingAs($this->user)
            ->post(route('cms.widget-templates.import'), [
                'json_file' => $file,
            ]);

        $response->assertSessionHas('error');
    }
}
