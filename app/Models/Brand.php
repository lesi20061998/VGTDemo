<?php
// MODIFIED: 2025-01-21

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
    use HasFactory, ProjectScoped, BelongsToTenant;

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'is_active', 'tenant_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    // Accessors
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }
}
