<?php

namespace App\Models;

use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxonomy extends Model
{
    use HasFactory, ProjectScoped, SoftDeletes;

    protected $fillable = [
        'project_id',
        'tenant_id',
        'name',
        'slug',
        'taxonomy',
        'description',
        'parent_id',
        'order',
        'status',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'parent_id')->orderBy('order');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'term_relationships', 'term_taxonomy_id', 'object_id')
            ->withPivot('order');
    }

    // Scopes for specific taxonomies
    public function scopeCategories($query)
    {
        return $query->where('taxonomy', 'category');
    }

    public function scopeBrands($query)
    {
        return $query->where('taxonomy', 'brand');
    }

    public function scopeTags($query)
    {
        return $query->where('taxonomy', 'tag');
    }

    // Helper methods
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getLevelAttribute(): int
    {
        return $this->meta_data['level'] ?? 0;
    }

    public function getImageAttribute(): ?string
    {
        return $this->meta_data['image'] ?? null;
    }

    public function getIsActiveAttribute(): bool
    {
        if (isset($this->meta_data['is_active'])) {
            return (bool) $this->meta_data['is_active'];
        }

        return $this->status === 'published';
    }
}
