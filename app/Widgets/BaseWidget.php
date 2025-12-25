<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

abstract class BaseWidget
{
    protected array $settings;
    protected array $metadata;
    protected string $variant = 'default';
    protected ?string $metadataPath = null;

    public function __construct(array $settings = [], string $variant = 'default')
    {
        $this->settings = $settings;
        $this->variant = $variant;
        $this->metadata = $this->loadMetadata();
        $this->validateSettings();
    }

    abstract public function render(): string;

    /**
     * Get the path to the widget metadata file
     */
    public static function getMetadataPath(): string
    {
        $reflection = new \ReflectionClass(static::class);
        $directory = dirname($reflection->getFileName());
        return $directory . '/widget.json';
    }

    /**
     * Load widget metadata from JSON file or fallback to getConfig()
     */
    protected function loadMetadata(): array
    {
        $metadataPath = static::getMetadataPath();
        
        if (File::exists($metadataPath)) {
            $cacheKey = 'widget_metadata_' . md5($metadataPath . File::lastModified($metadataPath));
            
            return Cache::remember($cacheKey, 3600, function () use ($metadataPath) {
                $content = File::get($metadataPath);
                $metadata = json_decode($content, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Invalid JSON in widget metadata: ' . json_last_error_msg());
                }
                
                return $this->validateMetadataSchema($metadata);
            });
        }
        
        // Fallback to legacy getConfig method
        if (method_exists(static::class, 'getConfig')) {
            return static::getConfig();
        }
        
        throw new \RuntimeException('Widget metadata not found: ' . $metadataPath);
    }

    /**
     * Validate metadata against schema
     */
    protected function validateMetadataSchema(array $metadata): array
    {
        $validator = Validator::make($metadata, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'category' => 'required|string|max:100',
            'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
            'author' => 'string|max:255',
            'icon' => 'string',
            'preview_image' => 'string',
            'variants' => 'array',
            'variants.*' => 'string',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string|in:text,textarea,image,gallery,select,checkbox,repeatable,nested,url,number,email,date',
            'fields.*.required' => 'boolean',
            'fields.*.default' => 'nullable',
            'fields.*.validation' => 'string',
            'fields.*.help' => 'string',
            'fields.*.options' => 'array',
            'fields.*.max_items' => 'integer|min:1',
            'fields.*.fields' => 'array', // For repeatable/nested fields
            'settings' => 'array',
            'settings.cacheable' => 'boolean',
            'settings.cache_duration' => 'integer|min:0',
            'settings.permissions' => 'array',
            'settings.dependencies' => 'array',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid widget metadata: ' . $validator->errors()->first());
        }

        return $metadata;
    }

    /**
     * Get widget metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get available variants
     */
    public function getVariants(): array
    {
        return $this->metadata['variants'] ?? ['default' => 'Default'];
    }

    /**
     * Get current variant
     */
    public function getVariant(): string
    {
        return $this->variant;
    }

    /**
     * Set variant
     */
    public function setVariant(string $variant): self
    {
        if (!array_key_exists($variant, $this->getVariants())) {
            throw new \InvalidArgumentException("Variant '{$variant}' not available for this widget");
        }
        
        $this->variant = $variant;
        return $this;
    }

    /**
     * Validate widget settings against metadata field definitions
     */
    public function validateSettings(): bool
    {
        $fields = $this->metadata['fields'] ?? [];
        
        $fieldTypeService = new \App\Services\FieldTypeService();
        $errors = $fieldTypeService->validateForm($fields, $this->settings);
        
        if (!empty($errors)) {
            $errorMessages = implode(', ', $errors);
            throw new \InvalidArgumentException('Widget settings validation failed: ' . $errorMessages);
        }
        
        return true;
    }

    /**
     * Get preview HTML for admin interface
     */
    public function getPreview(): string
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return '<div class="widget-preview-error">Preview Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Get widget field value with default fallback
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        
        // Check for default in metadata
        $fields = $this->metadata['fields'] ?? [];
        foreach ($fields as $field) {
            if ($field['name'] === $key && isset($field['default'])) {
                return $field['default'];
            }
        }
        
        return $default;
    }

    /**
     * Get all settings
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Set settings
     */
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        $this->validateSettings();
        return $this;
    }

    /**
     * CSS for the widget
     */
    public function css(): string
    {
        return '';
    }

    /**
     * JavaScript for the widget
     */
    public function js(): string
    {
        return '';
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        // This method should be overridden by widgets that haven't migrated to JSON metadata
        throw new \RuntimeException('Widget must implement getConfig() method or provide widget.json metadata file');
    }
}

