<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\BelongsToTenant;

class MenuItem extends Model
{
    use BelongsToTenant, ProjectScoped;
    
    protected $fillable = ['menu_id', 'parent_id', 'title', 'url', 'target', 'linkable_type', 'linkable_id', 'order', 'tenant_id'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }
    
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }
    
    public function getUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        if ($this->linkable) {
            // Generate URL based on linkable type
            switch ($this->linkable_type) {
                case 'App\\Models\\Post':
                    return route('frontend.page', $this->linkable->slug ?? $this->linkable->id);
                case 'App\\Models\\ProductCategory':
                    return route('frontend.category', $this->linkable->slug ?? $this->linkable->id);
                default:
                    return '#';
            }
        }
        
        return '#';
    }
}

