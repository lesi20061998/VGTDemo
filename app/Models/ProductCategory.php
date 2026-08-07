<?php

// MODIFIED: 2025-01-21

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use BelongsToTenant, HasFactory, ProjectScoped;

    /**
     * Maximum allowed category depth (0-based, so 3 means 4 levels: 0,1,2,3)
     */
    const MAX_DEPTH = 3;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 'level', 'path',
        'sort_order', 'is_active', 'meta_title', 'meta_description', 'tenant_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
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

    /**
     * Update levels and paths for all descendants when this category's hierarchy changes
     */
    public function updateDescendantsHierarchy(): void
    {
        foreach ($this->children as $child) {
            // Update child's level and path based on its parent's new level and path
            $child->level = $this->level + 1;
            $child->path = $this->path.'/'.$child->slug;
            $child->save();

            // Recursively update this child's descendants
            $child->updateDescendantsHierarchy();
        }
    }

    /**
     * Move this category and all its descendants to a new parent
     * This method provides more control over the moving process
     */
    public function moveToParent($newParentId = null): void
    {
        $oldLevel = $this->level;
        $oldPath = $this->path;

        // Calculate new level and path
        if ($newParentId) {
            $newParent = static::find($newParentId);
            $newLevel = $newParent->level + 1;

            // Check if new level would exceed maximum depth
            if ($newLevel > static::MAX_DEPTH) {
                throw new \InvalidArgumentException('Moving category would exceed maximum depth of '.(static::MAX_DEPTH + 1).' levels');
            }

            $this->level = $newLevel;
            $this->path = $newParent->path.'/'.$this->slug;
            $this->parent_id = $newParentId;
        } else {
            $this->level = 0;
            $this->path = $this->slug;
            $this->parent_id = null;
        }

        $this->save();

        // Update all descendants if level or path changed
        if ($oldLevel !== $this->level || $oldPath !== $this->path) {
            $this->updateDescendantsHierarchy();
        }
    }

    public function validateHierarchyConsistency(): bool
    {
        // Check if level exceeds maximum depth
        if ($this->level > static::MAX_DEPTH) {
            return false;
        }

        // Root categories must be level 0
        if (is_null($this->parent_id) && $this->level !== 0) {
            return false;
        }

        // Categories with parent must be exactly parent level + 1
        if (! is_null($this->parent_id)) {
            $parent = $this->parent;
            if (! $parent || $this->level !== ($parent->level + 1)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Fix hierarchy inconsistencies by recalculating levels
     */
    public function fixHierarchyConsistency(): void
    {
        // If this is a root category, level should be 0
        if (is_null($this->parent_id)) {
            $this->level = 0;
            $this->path = $this->slug;
        } else {
            // Calculate level based on parent
            $parent = $this->parent;
            if ($parent) {
                $this->level = $parent->level + 1;
                $this->path = $parent->path.'/'.$this->slug;
            }
        }

        $this->save();

        // Recursively fix all descendants
        $this->updateDescendantsHierarchy();
    }

    /**
     * Check if this category can have children (not at max depth)
     */
    public function canHaveChildren(): bool
    {
        return $this->level < static::MAX_DEPTH;
    }

    /**
     * Get categories that can be parents for this category
     * (categories that are not at max depth and not descendants of this category)
     */
    public function getPossibleParents()
    {
        $query = static::where('id', '!=', $this->id)
            ->where('level', '<', static::MAX_DEPTH);

        // Exclude descendants to prevent circular references
        if ($this->exists) {
            $descendants = $this->getDescendants();
            if ($descendants->isNotEmpty()) {
                $query->whereNotIn('id', $descendants->pluck('id'));
            }
        }

        return $query->orderBy('path')->get();
    }
}
