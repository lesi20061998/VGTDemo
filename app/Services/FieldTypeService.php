<?php

namespace App\Services;

use App\Contracts\FieldTypeInterface;
use App\Services\FieldTypes\TextField;
use App\Services\FieldTypes\TextareaField;
use App\Services\FieldTypes\SelectField;
use App\Services\FieldTypes\CheckboxField;
use App\Services\FieldTypes\ImageField;
use App\Services\FieldTypes\GalleryField;
use App\Services\FieldTypes\RepeatableField;
use App\Services\FieldTypes\UrlField;
use App\Services\FieldTypes\NumberField;
use App\Services\FieldTypes\EmailField;
use App\Services\FieldTypes\DateField;
use App\Services\FieldTypes\ColorField;
use App\Services\FieldTypes\RangeField;
use App\Services\FieldTypes\RelationshipField;
use App\Services\FieldTypes\PostObjectField;
use App\Services\FieldTypes\TaxonomyField;
use App\Services\FieldTypes\WysiwygField;

class FieldTypeService
{
    protected array $fieldTypes = [];

    public function __construct()
    {
        $this->registerDefaultFieldTypes();
    }

    /**
     * Register default field types
     */
    protected function registerDefaultFieldTypes(): void
    {
        // Basic fields
        $this->register(new TextField());
        $this->register(new TextareaField());
        $this->register(new WysiwygField());
        $this->register(new NumberField());
        $this->register(new EmailField());
        $this->register(new UrlField());
        $this->register(new DateField());
        
        // Choice fields
        $this->register(new SelectField());
        $this->register(new CheckboxField());
        
        // Media fields
        $this->register(new ImageField());
        $this->register(new GalleryField());
        $this->register(new ColorField());
        
        // Relational fields (ACF-like)
        $this->register(new RelationshipField());
        $this->register(new PostObjectField());
        $this->register(new TaxonomyField());
        
        // Layout fields
        $this->register(new RepeatableField());
        $this->register(new RangeField());
    }

    /**
     * Register a field type
     */
    public function register(FieldTypeInterface $fieldType): void
    {
        $this->fieldTypes[$fieldType::getTypeName()] = $fieldType;
    }

    /**
     * Get field type instance
     */
    public function get(string $type): ?FieldTypeInterface
    {
        return $this->fieldTypes[$type] ?? null;
    }

    /**
     * Check if field type exists
     */
    public function exists(string $type): bool
    {
        return isset($this->fieldTypes[$type]);
    }

    /**
     * Get all registered field types
     */
    public function getAll(): array
    {
        return $this->fieldTypes;
    }

    /**
     * Get field type names
     */
    public function getTypeNames(): array
    {
        return array_keys($this->fieldTypes);
    }

    /**
     * Render field HTML
     */
    public function renderField(array $fieldConfig, mixed $value = null): string
    {
        $type = $fieldConfig['type'] ?? 'text';
        $fieldType = $this->get($type);

        if (!$fieldType) {
            throw new \InvalidArgumentException("Field type '{$type}' not found");
        }

        return $fieldType->render($fieldConfig, $value);
    }

    /**
     * Validate field value
     */
    public function validateField(array $fieldConfig, mixed $value): bool
    {
        $type = $fieldConfig['type'] ?? 'text';
        $fieldType = $this->get($type);

        if (!$fieldType) {
            return false;
        }

        $rules = [];
        if (isset($fieldConfig['validation'])) {
            $rules = explode('|', $fieldConfig['validation']);
        }

        return $fieldType->validate($value, $rules);
    }

    /**
     * Transform field value
     */
    public function transformField(array $fieldConfig, mixed $value): mixed
    {
        $type = $fieldConfig['type'] ?? 'text';
        $fieldType = $this->get($type);

        if (!$fieldType) {
            return $value;
        }

        return $fieldType->transform($value);
    }

    /**
     * Render form from field configurations
     */
    public function renderForm(array $fields, array $values = []): string
    {
        $html = '';

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $values[$fieldName] ?? null;

            try {
                $html .= $this->renderField($field, $fieldValue);
            } catch (\Exception $e) {
                // Log error and show fallback
                \Log::error("Field render error for {$fieldName}: " . $e->getMessage());
                $html .= "<div class=\"mb-4 p-3 bg-red-50 border border-red-200 rounded\">";
                $html .= "<p class=\"text-red-600 text-sm\">Error rendering field '{$fieldName}': " . htmlspecialchars($e->getMessage()) . "</p>";
                $html .= "</div>";
            }
        }

        return $html;
    }

    /**
     * Validate form data against field configurations
     */
    public function validateForm(array $fields, array $data): array
    {
        $errors = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $data[$fieldName] ?? null;

            // Check required fields
            if (($field['required'] ?? false) && empty($fieldValue)) {
                $errors[$fieldName] = "Field '{$field['label']}' is required";
                continue;
            }

            // Validate field value
            if ($fieldValue !== null && !$this->validateField($field, $fieldValue)) {
                $errors[$fieldName] = "Field '{$field['label']}' has invalid value";
            }
        }

        return $errors;
    }

    /**
     * Transform form data
     */
    public function transformFormData(array $fields, array $data): array
    {
        $transformed = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $data[$fieldName] ?? null;

            if ($fieldValue !== null) {
                $transformed[$fieldName] = $this->transformField($field, $fieldValue);
            }
        }

        return $transformed;
    }

    /**
     * Get field type information
     */
    public function getFieldTypeInfo(): array
    {
        $info = [];

        foreach ($this->fieldTypes as $type => $fieldType) {
            $info[$type] = [
                'name' => $type,
                'class' => get_class($fieldType),
                'description' => $this->getFieldTypeDescription($type)
            ];
        }

        return $info;
    }

    /**
     * Get field type description
     */
    protected function getFieldTypeDescription(string $type): string
    {
        $descriptions = [
            'text' => 'Single line text input',
            'textarea' => 'Multi-line text input',
            'wysiwyg' => 'Rich text editor (WYSIWYG)',
            'select' => 'Dropdown selection',
            'checkbox' => 'Boolean checkbox',
            'image' => 'Image file upload',
            'gallery' => 'Multiple image gallery',
            'repeatable' => 'Repeatable group of fields',
            'url' => 'URL input with validation',
            'number' => 'Numeric input',
            'email' => 'Email input with validation',
            'date' => 'Date picker',
            'color' => 'Color picker',
            'range' => 'Range slider',
            'relationship' => 'Link to Products/Posts (multiple)',
            'post_object' => 'Link to single Product/Post',
            'taxonomy' => 'Select Category/Brand',
        ];

        return $descriptions[$type] ?? 'Custom field type';
    }
}