<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'template',
        'status',
        'meta_title',
        'meta_description',
        'seo_data'
    ];

    protected $casts = [
        'seo_data' => 'array'
    ];

    /**
     * Get the sections for the page
     */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->ordered();
    }

    /**
     * Get active sections for the page
     */
    public function activeSections(): HasMany
    {
        return $this->hasMany(PageSection::class)->active()->ordered();
    }

    /**
     * Scope for published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get rendered page content with sections
     */
    public function getRenderedContent(): string
    {
        $content = $this->content ?? '';
        
        // Add rendered sections
        $sectionsHtml = '';
        foreach ($this->activeSections as $section) {
            $sectionsHtml .= $section->getRenderedContent();
        }
        
        return $content . $sectionsHtml;
    }

    /**
     * Add a section to the page
     */
    public function addSection(string $type, array $settings = [], int $order = null): PageSection
    {
        if ($order === null) {
            $order = $this->sections()->max('order') + 1;
        }

        return $this->sections()->create([
            'type' => $type,
            'settings' => $settings,
            'order' => $order,
            'is_active' => true
        ]);
    }
}