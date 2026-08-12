<?php

namespace App\Widgets;

use App\Services\FieldTypeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

abstract class BaseWidget
{
    protected array $settings;

    protected array $metadata;

    protected string $variant = 'default';

    protected ?string $metadataPath = null;

    public function __construct(array $settings = [], string $variant = 'default')
    {
        $this->settings = $settings;
        $this->metadata = $this->loadMetadata();
        $this->setVariant($variant);
        $this->validateSettings();
    }

    /**
     * Parse image URL safely from string or array (e.g. from media picker)
     */
    protected function parseImageUrl(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        if (\is_string($value)) {
            return trim($value);
        }

        if (\is_array($value)) {
            return $value['url'] ?? $value[0] ?? '';
        }

        return '';
    }

    /**
     * Build background CSS style & extra classes dynamically.
     * Returns an array with ['style' => '...', 'classes' => [...]]
     */
    protected function getWrapperBackgroundInfo(): array
    {
        $bgType = $this->get('bg_type', 'color');
        $rawImage = $this->get('background_image', '');
        $bgColor = trim((string) $this->get('bg_color', ''));

        $imageUrl = $this->parseImageUrl($rawImage);

        $styleParts = [];
        $extraClasses = [];

        // Priority 1: Background Image if provided
        if (! empty($imageUrl)) {
            $styleParts[] = "background-image: url('{$imageUrl}')";
            $styleParts[] = 'background-size: cover';
            $styleParts[] = 'background-position: center';
            $styleParts[] = 'background-repeat: no-repeat';

            if (! empty($bgColor) && (str_starts_with($bgColor, '#') || str_starts_with($bgColor, 'rgb'))) {
                $styleParts[] = "background-color: {$bgColor}";
            }
        } elseif (! empty($bgColor)) {
            // Priority 2: Color if provided
            if (str_starts_with($bgColor, '#') || str_starts_with($bgColor, 'rgb')) {
                $styleParts[] = "background-color: {$bgColor}";
            } elseif (str_contains($bgColor, 'gradient(')) {
                $styleParts[] = "background: {$bgColor}";
            } elseif (str_starts_with($bgColor, 'bg-')) {
                $extraClasses[] = $bgColor;
            } else {
                $styleParts[] = "background-color: {$bgColor}";
            }
        }

        return [
            'style' => ! empty($styleParts) ? implode('; ', $styleParts).';' : '',
            'classes' => $extraClasses,
        ];
    }

    /**
     * Build a combined inline style string from background info + margin/padding settings.
     * Returns the full style="..." attribute string, or empty string if nothing to apply.
     */
    protected function buildWrapperStyleAttribute(): string
    {
        $bgInfo = $this->getWrapperBackgroundInfo();
        $parts = [];

        if (! empty($bgInfo['style'])) {
            $parts[] = $bgInfo['style'];
        }

        foreach (['margin_top', 'margin_bottom', 'margin_left', 'margin_right', 'padding_top', 'padding_bottom', 'padding_left', 'padding_right'] as $prop) {
            $val = $this->get($prop);
            if ($val !== null && $val !== '') {
                if (is_numeric($val)) {
                    $val .= 'px';
                }
                $cssProp = str_replace('_', '-', $prop);
                $parts[] = "{$cssProp}: {$val}";
            }
        }

        return ! empty($parts) ? ' style="'.implode('; ', $parts).';"' : '';
    }

    /**
     * Get wrapper background CSS classes (from getWrapperBackgroundInfo).
     */
    protected function getWrapperBgClasses(): array
    {
        return $this->getWrapperBackgroundInfo()['classes'];
    }

    /**
     * Build typography class + inline style for title or description.
     *
     * @param  string  $weightKey  e.g. 'title_font_weight' or 'description_font_weight'
     * @param  string  $colorKey  e.g. 'title_color' or 'description_color'
     * @param  string  $defaultWeight  e.g. 'font-bold'
     * @return array{class: string, style: string}
     */
    protected function buildTypography(string $weightKey, string $colorKey, string $defaultWeight = ''): array
    {
        $weight = $this->get($weightKey, $defaultWeight);
        $color = $this->get($colorKey);

        return [
            'class' => $weight ?: '',
            'style' => $color ? ' style="color: '.$color.';"' : '',
        ];
    }

    /**
     * Wrap rendered widget HTML with custom CSS, JS, body code, and footer code injections.
     */
    protected function wrapWithCodeInjections(string $innerHtml): string
    {
        $html = '';

        $customCss = $this->get('custom_css', '');
        if ($customCss) {
            $html .= '<style>'.$customCss.'</style>';
        }

        $bodyCode = $this->get('body_code', '');
        if ($bodyCode) {
            $html .= $bodyCode."\n";
        }

        $html .= $innerHtml;

        $customJs = $this->get('custom_js', '');
        if ($customJs) {
            $html .= '<script>'.$customJs.'</script>';
        }

        $footerCode = $this->get('footer_code', '');
        if ($footerCode) {
            $html .= "\n".$footerCode;
        }

        return $html;
    }

    abstract public function render(): string;

    /**
     * Get the path to the widget metadata file
     */
    public static function getMetadataPath(): string
    {
        $reflection = new \ReflectionClass(static::class);
        $directory = dirname($reflection->getFileName());

        return $directory.'/widget.json';
    }

    /**
     * Load widget metadata from JSON file or fallback to getConfig()
     */
    protected function loadMetadata(): array
    {
        $metadataPath = static::getMetadataPath();

        if (File::exists($metadataPath)) {
            $cacheKey = 'widget_metadata_'.md5($metadataPath.File::lastModified($metadataPath));

            return Cache::remember($cacheKey, 3600, function () use ($metadataPath) {
                $content = File::get($metadataPath);
                $metadata = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Invalid JSON in widget metadata: '.json_last_error_msg());
                }

                return $this->validateMetadataSchema($metadata);
            });
        }

        // Fallback to legacy getConfig method
        if (method_exists(static::class, 'getConfig')) {
            return static::getConfig();
        }

        throw new \RuntimeException('Widget metadata not found: '.$metadataPath);
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
            'fields.*.type' => 'required|string|in:text,textarea,wysiwyg,image,gallery,video,select,checkbox,repeatable,nested,url,number,email,date,color,range,relationship,post_object,taxonomy',
            'fields.*.required' => 'boolean',
            'fields.*.default' => 'nullable',
            'fields.*.validation' => 'string',
            'fields.*.help' => 'string',
            'fields.*.placeholder' => 'string',
            'fields.*.show_if' => 'array',
            'fields.*.options' => 'array',
            'fields.*.max_items' => 'integer|min:1',
            'fields.*.fields' => 'array',
            'settings' => 'array',
            'settings.cacheable' => 'boolean',
            'settings.cache_duration' => 'integer|min:0',
            'settings.permissions' => 'array',
            'settings.dependencies' => 'array',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid widget metadata: '.$validator->errors()->first());
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
        if (! array_key_exists($variant, $this->getVariants())) {
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

        $fieldTypeService = new FieldTypeService;
        $errors = $fieldTypeService->validateForm($fields, $this->settings);

        if (! empty($errors)) {
            $errorMessages = implode(', ', $errors);
            throw new \InvalidArgumentException('Widget settings validation failed: '.$errorMessages);
        }

        return true;
    }

    /**
     * Get preview HTML for admin interface
     */
    public function getPreview(): string
    {
        try {
            return $this->css() . $this->render() . $this->js();
        } catch (\Exception $e) {
            return '<div class="widget-preview-error" style="padding:1rem;color:#ef4444;font-family:sans-serif;">Preview Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
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
