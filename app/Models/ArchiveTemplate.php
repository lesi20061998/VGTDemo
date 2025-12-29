<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveTemplate extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type',           // product, post, category
        'slug',           // archive-product, archive-post-news
        'description',
        'template_code',  // Blade template code
        'template_css',
        'template_js',
        'config',         // JSON config: pagination, columns, filters, etc.
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (session('current_tenant_id')) {
                $query->where('tenant_id', session('current_tenant_id'));
            }
        });

        static::creating(function ($template) {
            if (!$template->tenant_id && session('current_tenant_id')) {
                $template->tenant_id = session('current_tenant_id');
            }
        });

        // Ensure only one default per type
        static::saving(function ($template) {
            if ($template->is_default) {
                static::where('type', $template->type)
                    ->where('id', '!=', $template->id ?? 0)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get default template for a type
     */
    public static function getDefault(string $type): ?self
    {
        return static::where('type', $type)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Render the archive template
     */
    public function render(array $data = []): string
    {
        try {
            // Check for file-based template first
            $viewPath = "archives.{$this->type}.{$this->slug}";
            if (\View::exists($viewPath)) {
                return view($viewPath, $data)->render();
            }

            // Fallback to database template
            if ($this->template_code) {
                $html = \Blade::render($this->template_code, array_merge($data, [
                    'config' => $this->config ?? [],
                    'template' => $this,
                ]));

                // Inject CSS
                if ($this->template_css) {
                    $html = "<style>{$this->template_css}</style>" . $html;
                }

                // Inject JS
                if ($this->template_js) {
                    $html .= "<script>{$this->template_js}</script>";
                }

                return $html;
            }

            return '';
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return "<div class='bg-red-100 p-4 rounded'>Archive Template Error: {$e->getMessage()}</div>";
            }
            return '';
        }
    }

    /**
     * Get CSS from file
     */
    public function getCss(): string
    {
        $path = resource_path("views/archives/{$this->type}/{$this->slug}/style.css");
        return \File::exists($path) ? \File::get($path) : ($this->template_css ?? '');
    }

    /**
     * Get JS from file
     */
    public function getJs(): string
    {
        $path = resource_path("views/archives/{$this->type}/{$this->slug}/script.js");
        return \File::exists($path) ? \File::get($path) : ($this->template_js ?? '');
    }
}
