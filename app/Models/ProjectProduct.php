<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProduct extends Model
{
    use HasFactory;

    protected $connection = 'project';

    protected $table = 'products_enhanced';

    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'sku', 'price', 'sale_price',
        'stock_quantity', 'manage_stock', 'stock_status', 'weight', 'dimensions',
        'product_category_id', 'brand_id', 'status', 'featured', 'gallery', 'meta_title',
        'meta_description', 'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'manage_stock' => 'boolean',
        'featured' => 'boolean',
        'gallery' => 'array',
        'dimensions' => 'array',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProjectProductCategory::class, 'product_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(ProjectBrand::class, 'brand_id');
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock(Builder $query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }
}
