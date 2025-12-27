<?php

namespace App\Livewire\Admin;

use App\Models\WidgetTemplate;
use App\Services\FieldTypeService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('cms.layouts.app')]
class WidgetTemplateBuilder extends Component
{
    public ?WidgetTemplate $template = null;
    
    // Template info
    public string $name = '';
    public string $type = '';
    public string $category = 'general';
    public string $description = '';
    public string $icon = 'cube';
    public bool $is_active = true;
    
    // Template code (Blade/PHP)
    public string $template_code = '';
    public string $template_css = '';
    public string $template_js = '';
    
    // Fields builder
    public array $fields = [];
    
    // Available field types
    public array $fieldTypes = [];
    
    // UI state
    public bool $showFieldModal = false;
    public int $editingFieldIndex = -1;
    public array $currentField = [];
    public string $activeTab = 'fields'; // fields, code, css, js
    
    // Categories
    public array $categories = [
        'general' => 'Chung',
        'hero' => 'Hero/Banner',
        'content' => 'Nội dung',
        'product' => 'Sản phẩm',
        'marketing' => 'Marketing',
        'layout' => 'Layout',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'description' => 'nullable|string|max:500',
    ];

    // Auto-generate type from name
    public function updatedName($value): void
    {
        if (!$this->template) { // Only auto-generate for new templates
            $this->type = $this->generateSlug($value);
        }
    }

    protected function generateSlug(string $text): string
    {
        // Convert Vietnamese to ASCII
        $text = $this->removeVietnameseAccents($text);
        // Convert to lowercase
        $text = strtolower($text);
        // Replace spaces and special chars with underscore
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);
        // Remove leading/trailing underscores
        $text = trim($text, '_');
        return $text;
    }

    protected function removeVietnameseAccents(string $str): string
    {
        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ',
            'À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
            'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
            'Ì','Í','Ị','Ỉ','Ĩ',
            'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
            'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
            'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
            'Đ'
        ];
        $noAccents = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd',
            'A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
            'E','E','E','E','E','E','E','E','E','E','E',
            'I','I','I','I','I',
            'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
            'U','U','U','U','U','U','U','U','U','U','U',
            'Y','Y','Y','Y','Y',
            'D'
        ];
        return str_replace($accents, $noAccents, $str);
    }

    public function mount(?int $id = null): void
    {
        $fieldTypeService = new FieldTypeService();
        $this->fieldTypes = $fieldTypeService->getFieldTypeInfo();
        
        if ($id) {
            $this->template = WidgetTemplate::findOrFail($id);
            $this->name = $this->template->name;
            $this->type = $this->template->type;
            $this->category = $this->template->category;
            $this->description = $this->template->description ?? '';
            $this->icon = $this->template->icon ?? 'cube';
            $this->is_active = $this->template->is_active;
            $this->fields = $this->template->config_schema['fields'] ?? [];
            
            // Load code from files if they exist
            $this->loadCodeFromFiles();
        } else {
            // Default template code for new widgets
            $this->template_code = $this->getDefaultTemplateCode();
        }
        
        $this->resetCurrentField();
    }

    /**
     * Load template code from existing files
     */
    protected function loadCodeFromFiles(): void
    {
        $className = $this->getWidgetClassName();
        $widgetDir = app_path("Widgets/Custom/{$className}");
        
        // Load Blade template
        $bladePath = "{$widgetDir}/view.blade.php";
        if (\File::exists($bladePath)) {
            $this->template_code = \File::get($bladePath);
        } else {
            $this->template_code = $this->getDefaultTemplateCode();
        }
        
        // Load CSS
        $cssPath = "{$widgetDir}/style.css";
        if (\File::exists($cssPath)) {
            $this->template_css = \File::get($cssPath);
        }
        
        // Load JS
        $jsPath = "{$widgetDir}/script.js";
        if (\File::exists($jsPath)) {
            $this->template_js = \File::get($jsPath);
        }
    }
    
    protected function getDefaultTemplateCode(): string
    {
        return <<<'BLADE'
{{-- Widget Template --}}
{{-- Available variables: $settings (array of field values), $widget (WidgetTemplate model) --}}
{{-- Available helpers: $products($limit), $posts($limit), $categories() --}}

<div class="widget-container p-4">
    {{-- Access field values via $settings --}}
    @if(!empty($settings['title']))
        <h2 class="text-2xl font-bold mb-4">{{ $settings['title'] }}</h2>
    @endif
    
    {{-- Example: Display products --}}
    {{-- 
    @php
        $items = $products(6);
    @endphp
    
    <div class="grid grid-cols-3 gap-4">
        @foreach($items as $item)
            <div class="border rounded p-3">
                <h3>{{ $item->name }}</h3>
                <p>{{ $item->price }}</p>
            </div>
        @endforeach
    </div>
    --}}
    
    {{-- Your custom code here --}}
</div>
BLADE;
    }
    
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function resetCurrentField(): void
    {
        $this->currentField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'default' => '',
            'help' => '',
            // Text/Textarea
            'placeholder' => '',
            'rows' => 4,
            // WYSIWYG
            'toolbar' => 'full',
            'height' => 300,
            // Number/Range
            'min' => null,
            'max' => null,
            'step' => 1,
            // Select
            'options' => [],
            'multiple' => false,
            // Image/Gallery
            'return_format' => 'url',
            'preview_size' => 'medium',
            // Relationship/Post Object
            'post_type' => 'product',
            // Taxonomy
            'taxonomy' => 'category',
            'field_type' => 'select',
            // Repeater
            'layout' => 'table',
            'button_label' => 'Add Row',
            'fields' => [],
            // Date
            'display_format' => 'd/m/Y',
        ];
    }

    public function openAddFieldModal(): void
    {
        $this->resetCurrentField();
        $this->editingFieldIndex = -1;
        $this->showFieldModal = true;
    }

    public function openEditFieldModal(int $index): void
    {
        $this->currentField = $this->fields[$index];
        $this->editingFieldIndex = $index;
        $this->showFieldModal = true;
    }

    public function closeFieldModal(): void
    {
        $this->showFieldModal = false;
        $this->resetCurrentField();
        $this->editingFieldIndex = -1;
    }

    public function saveField(): void
    {
        $this->validate([
            'currentField.name' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
            'currentField.label' => 'required|string|max:255',
            'currentField.type' => 'required|string',
        ], [
            'currentField.name.required' => 'Tên field là bắt buộc',
            'currentField.name.regex' => 'Tên field chỉ được dùng chữ thường, số và dấu gạch dưới',
            'currentField.label.required' => 'Nhãn hiển thị là bắt buộc',
        ]);

        $fieldType = $this->currentField['type'];
        
        // Base field config
        $field = [
            'name' => $this->currentField['name'],
            'label' => $this->currentField['label'],
            'type' => $fieldType,
            'required' => $this->currentField['required'] ?? false,
            'default' => $this->currentField['default'] ?? '',
            'help' => $this->currentField['help'] ?? '',
        ];

        // Add type-specific settings
        $typeSettings = $this->getTypeSpecificSettings($fieldType);
        foreach ($typeSettings as $key) {
            if (isset($this->currentField[$key]) && $this->currentField[$key] !== '' && $this->currentField[$key] !== null) {
                $field[$key] = $this->currentField[$key];
            }
        }

        // Add options for select/radio type
        if (in_array($fieldType, ['select', 'radio']) && !empty($this->currentField['options'])) {
            $field['options'] = array_values(array_filter($this->currentField['options'], fn($opt) => !empty($opt['value']) || !empty($opt['label'])));
        }

        // Add nested fields for repeatable/repeater
        if (in_array($fieldType, ['repeatable', 'repeater']) && !empty($this->currentField['fields'])) {
            $field['fields'] = array_values(array_filter($this->currentField['fields'], fn($f) => !empty($f['name'])));
        }

        if ($this->editingFieldIndex >= 0) {
            $this->fields[$this->editingFieldIndex] = $field;
        } else {
            $this->fields[] = $field;
        }

        $this->closeFieldModal();
    }

    /**
     * Get type-specific settings keys for each field type
     */
    protected function getTypeSpecificSettings(string $type): array
    {
        return match ($type) {
            'text', 'email', 'url' => ['placeholder'],
            'textarea' => ['placeholder', 'rows'],
            'wysiwyg' => ['toolbar', 'height'],
            'number' => ['min', 'max', 'placeholder'],
            'select' => ['multiple', 'placeholder'],
            'radio' => [],
            'checkbox' => [],
            'image' => ['return_format', 'preview_size'],
            'gallery' => ['min', 'max', 'return_format'],
            'relationship' => ['post_type', 'taxonomy', 'min', 'max', 'return_format'],
            'post_object' => ['post_type', 'return_format', 'multiple'],
            'taxonomy' => ['taxonomy', 'field_type', 'return_format', 'multiple'],
            'repeatable', 'repeater' => ['min', 'max', 'layout', 'button_label'],
            'color' => [],
            'date' => ['display_format', 'return_format'],
            'range' => ['min', 'max', 'step'],
            default => [],
        };
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function moveFieldUp(int $index): void
    {
        if ($index > 0) {
            $temp = $this->fields[$index - 1];
            $this->fields[$index - 1] = $this->fields[$index];
            $this->fields[$index] = $temp;
        }
    }

    public function moveFieldDown(int $index): void
    {
        if ($index < count($this->fields) - 1) {
            $temp = $this->fields[$index + 1];
            $this->fields[$index + 1] = $this->fields[$index];
            $this->fields[$index] = $temp;
        }
    }

    public function addSelectOption(): void
    {
        $this->currentField['options'][] = ['value' => '', 'label' => ''];
    }

    public function removeSelectOption(int $index): void
    {
        unset($this->currentField['options'][$index]);
        $this->currentField['options'] = array_values($this->currentField['options']);
    }

    public function addRepeatableField(): void
    {
        $this->currentField['fields'][] = [
            'name' => '',
            'label' => '',
            'type' => 'text',
        ];
    }

    public function removeRepeatableField(int $index): void
    {
        unset($this->currentField['fields'][$index]);
        $this->currentField['fields'] = array_values($this->currentField['fields']);
    }

    public function save(): void
    {
        $this->validate();

        // Generate widget files instead of saving to database
        $this->generateWidgetFiles();

        $configSchema = [
            'fields' => $this->fields,
        ];

        // Only save basic info to database - code is in files
        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'description' => $this->description,
            'icon' => $this->icon,
            'config_schema' => $configSchema,
            'default_settings' => $this->getDefaultSettings(),
            'is_active' => $this->is_active,
        ];

        if ($this->template) {
            $this->template->update($data);
            session()->flash('success', 'Widget template đã được cập nhật! Files đã được tạo tại app/Widgets/Custom/' . $this->getWidgetClassName());
        } else {
            WidgetTemplate::create($data);
            session()->flash('success', 'Widget template đã được tạo! Files đã được tạo tại app/Widgets/Custom/' . $this->getWidgetClassName());
        }

        // Clear widget cache
        \App\Widgets\WidgetRegistry::clearCache();

        // Redirect based on context
        $projectCode = request()->route('projectCode');
        if ($projectCode) {
            $this->redirect(route('project.admin.widget-templates.index', $projectCode));
        } else {
            $this->redirect(route('cms.widget-templates.index'));
        }
    }

    /**
     * Generate widget class name from type
     */
    protected function getWidgetClassName(): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->type)));
    }

    /**
     * Generate all widget files
     */
    protected function generateWidgetFiles(): void
    {
        $className = $this->getWidgetClassName();
        $widgetDir = app_path("Widgets/Custom/{$className}");

        // Create directory if not exists
        if (!\File::isDirectory($widgetDir)) {
            \File::makeDirectory($widgetDir, 0755, true);
        }

        // Generate widget.json metadata
        $this->generateMetadataFile($widgetDir);

        // Generate Widget PHP class
        $this->generateWidgetClass($widgetDir, $className);

        // Generate Blade view
        $this->generateBladeView($className);

        // Generate CSS file if has custom CSS
        if (!empty($this->template_css)) {
            $this->generateCssFile($className);
        }

        // Generate JS file if has custom JS
        if (!empty($this->template_js)) {
            $this->generateJsFile($className);
        }
    }

    /**
     * Generate widget.json metadata file
     */
    protected function generateMetadataFile(string $widgetDir): void
    {
        $metadata = [
            'name' => $this->name,
            'description' => $this->description ?: 'Custom widget created from admin',
            'category' => $this->category,
            'icon' => $this->icon,
            'version' => '1.0.0',
            'author' => auth()->user()->name ?? 'Admin',
            'tags' => [$this->category, 'custom'],
            'variants' => [
                'default' => 'Mặc định',
            ],
            'fields' => $this->fields,
            'settings' => [
                'cacheable' => true,
                'cache_duration' => 3600,
                'permissions' => [],
                'dependencies' => [],
            ],
        ];

        \File::put(
            "{$widgetDir}/widget.json",
            json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Generate Widget PHP class
     */
    protected function generateWidgetClass(string $widgetDir, string $className): void
    {
        $namespace = "App\\Widgets\\Custom\\{$className}";
        $viewName = "widgets.custom.{$this->type}";
        
        $hasCss = !empty($this->template_css);
        $hasJs = !empty($this->template_js);

        $classContent = <<<PHP
<?php

namespace {$namespace};

use App\Widgets\BaseWidget;

class {$className}Widget extends BaseWidget
{
    public function render(): string
    {
        return view('{$viewName}', [
            'settings' => \$this->settings,
            'variant' => \$this->variant,
        ])->render();
    }

    public static function getMetadataPath(): string
    {
        return __DIR__ . '/widget.json';
    }

PHP;

        // Add CSS method if has custom CSS
        if ($hasCss) {
            $classContent .= <<<PHP

    public function css(): string
    {
        return '<link rel="stylesheet" href="' . asset('css/widgets/{$this->type}.css') . '">';
    }

PHP;
        }

        // Add JS method if has custom JS
        if ($hasJs) {
            $classContent .= <<<PHP

    public function js(): string
    {
        return '<script src="' . asset('js/widgets/{$this->type}.js') . '"></script>';
    }

PHP;
        }

        $classContent .= "}\n";

        \File::put("{$widgetDir}/{$className}Widget.php", $classContent);
    }

    /**
     * Generate Blade view file
     */
    protected function generateBladeView(string $className): void
    {
        $viewDir = resource_path('views/widgets/custom');
        
        if (!\File::isDirectory($viewDir)) {
            \File::makeDirectory($viewDir, 0755, true);
        }

        $bladeContent = $this->template_code;
        
        // If no custom code, generate default template
        if (empty($bladeContent)) {
            $bladeContent = $this->generateDefaultBladeTemplate();
        }

        \File::put("{$viewDir}/{$this->type}.blade.php", $bladeContent);
    }

    /**
     * Generate default Blade template from fields
     */
    protected function generateDefaultBladeTemplate(): string
    {
        $template = "{{-- Widget: {$this->name} --}}\n";
        $template .= "{{-- Generated: " . now()->format('Y-m-d H:i:s') . " --}}\n\n";
        $template .= "<div class=\"widget widget-{$this->type}\">\n";

        foreach ($this->fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            $label = $field['label'];

            switch ($type) {
                case 'image':
                    $template .= "    @if(!empty(\$settings['{$name}']))\n";
                    $template .= "        <img src=\"{{ \$settings['{$name}'] }}\" alt=\"{$label}\" class=\"widget-image\">\n";
                    $template .= "    @endif\n\n";
                    break;

                case 'gallery':
                    $template .= "    @if(!empty(\$settings['{$name}']))\n";
                    $template .= "        <div class=\"widget-gallery grid grid-cols-3 gap-4\">\n";
                    $template .= "            @foreach(\$settings['{$name}'] as \$image)\n";
                    $template .= "                <img src=\"{{ \$image }}\" alt=\"\" class=\"w-full h-auto rounded\">\n";
                    $template .= "            @endforeach\n";
                    $template .= "        </div>\n";
                    $template .= "    @endif\n\n";
                    break;

                case 'textarea':
                case 'wysiwyg':
                    $template .= "    @if(!empty(\$settings['{$name}']))\n";
                    $template .= "        <div class=\"widget-content\">{!! \$settings['{$name}'] !!}</div>\n";
                    $template .= "    @endif\n\n";
                    break;

                case 'repeatable':
                case 'repeater':
                    $template .= "    @if(!empty(\$settings['{$name}']))\n";
                    $template .= "        <div class=\"widget-repeater\">\n";
                    $template .= "            @foreach(\$settings['{$name}'] as \$item)\n";
                    $template .= "                <div class=\"repeater-item\">\n";
                    if (!empty($field['fields'])) {
                        foreach ($field['fields'] as $subField) {
                            $subName = $subField['name'];
                            $template .= "                    <span>{{ \$item['{$subName}'] ?? '' }}</span>\n";
                        }
                    }
                    $template .= "                </div>\n";
                    $template .= "            @endforeach\n";
                    $template .= "        </div>\n";
                    $template .= "    @endif\n\n";
                    break;

                default:
                    $template .= "    @if(!empty(\$settings['{$name}']))\n";
                    $template .= "        <div class=\"widget-field widget-{$name}\">{{ \$settings['{$name}'] }}</div>\n";
                    $template .= "    @endif\n\n";
            }
        }

        $template .= "</div>\n";

        return $template;
    }

    /**
     * Generate CSS file
     */
    protected function generateCssFile(string $className): void
    {
        $cssDir = public_path('css/widgets');
        
        if (!\File::isDirectory($cssDir)) {
            \File::makeDirectory($cssDir, 0755, true);
        }

        $cssContent = "/* Widget: {$this->name} */\n";
        $cssContent .= "/* Generated: " . now()->format('Y-m-d H:i:s') . " */\n\n";
        $cssContent .= $this->template_css;

        \File::put("{$cssDir}/{$this->type}.css", $cssContent);
    }

    /**
     * Generate JS file
     */
    protected function generateJsFile(string $className): void
    {
        $jsDir = public_path('js/widgets');
        
        if (!\File::isDirectory($jsDir)) {
            \File::makeDirectory($jsDir, 0755, true);
        }

        $jsContent = "/* Widget: {$this->name} */\n";
        $jsContent .= "/* Generated: " . now()->format('Y-m-d H:i:s') . " */\n\n";
        $jsContent .= $this->template_js;

        \File::put("{$jsDir}/{$this->type}.js", $jsContent);
    }
    
    /**
     * Preview the template with sample data
     */
    public function previewTemplate(): string
    {
        if (empty($this->template_code)) {
            return '<p class="text-gray-500">Chưa có code template</p>';
        }
        
        try {
            // Create sample settings from fields
            $sampleSettings = $this->getDefaultSettings();
            
            return \Blade::render($this->template_code, [
                'settings' => $sampleSettings,
                'widget' => $this->template,
                'products' => fn($limit = 10) => \App\Models\Product::take($limit)->get(),
                'posts' => fn($limit = 10) => \App\Models\Post::take($limit)->get(),
                'categories' => fn() => \App\Models\Category::all(),
            ]);
        } catch (\Exception $e) {
            return "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    protected function getDefaultSettings(): array
    {
        $defaults = [];
        foreach ($this->fields as $field) {
            if (isset($field['default']) && $field['default'] !== '') {
                $defaults[$field['name']] = $field['default'];
            }
        }
        return $defaults;
    }

    public function render()
    {
        return view('livewire.admin.widget-template-builder');
    }
}
