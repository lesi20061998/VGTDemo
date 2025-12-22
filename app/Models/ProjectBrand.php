<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBrand extends Model
{
    use HasFactory;

    protected $connection = 'project';

    protected $table = 'brands';

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'is_active',
        'meta_title', 'meta_description', 'project_id', 'tenant_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Multi-site: Hide project_id since each project has separate database
    protected $hidden = [
        'project_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = \Illuminate\Support\Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = \Illuminate\Support\Str::slug($brand->name);
            }
        });
    }

    // Relationships
    public function products()
    {
        return $this->hasMany(ProjectProduct::class, 'brand_id');
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
}
