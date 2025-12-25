<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'settings',
        'order',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the page that owns the section
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Scope for active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered sections
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get rendered content for this section
     */
    public function getRenderedContent(): string
    {
        $renderingService = new \App\Services\WidgetRenderingService();
        
        return $renderingService->render(
            $this->type,
            $this->settings ?? [],
            $this->settings['variant'] ?? 'default'
        );
    }

    /**
     * Move section up in order
     */
    public function moveUp(): bool
    {
        $previousSection = static::where('page_id', $this->page_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousSection) {
            $tempOrder = $this->order;
            $this->order = $previousSection->order;
            $previousSection->order = $tempOrder;
            
            $this->save();
            $previousSection->save();
            
            return true;
        }

        return false;
    }

    /**
     * Move section down in order
     */
    public function moveDown(): bool
    {
        $nextSection = static::where('page_id', $this->page_id)
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextSection) {
            $tempOrder = $this->order;
            $this->order = $nextSection->order;
            $nextSection->order = $tempOrder;
            
            $this->save();
            $nextSection->save();
            
            return true;
        }

        return false;
    }

    /**
     * Reorder sections for a page
     */
    public static function reorderSections(int $pageId, array $sectionIds): void
    {
        foreach ($sectionIds as $order => $sectionId) {
            static::where('id', $sectionId)
                ->where('page_id', $pageId)
                ->update(['order' => $order]);
        }
    }
}