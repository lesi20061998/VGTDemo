<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProductCategory extends Model
{
    use HasFactory;

    protected $connection = 'project';

    protected $table = 'product_categories';

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 'level', 'path',
        'sort_order', 'is_active', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ProjectProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectProductCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(ProjectProduct::class, 'product_category_id');
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots(Builder $query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // Methods
    public function getAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    public function getDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }

        return $descendants;
    }
}
