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
        'product_category_id', 'brand_id', 'status', 'is_featured', 'is_favorite', 'is_bestseller', 'gallery', 'meta_title',
        'meta_description', 'has_price', 'featured_image', 'badges',
        'focus_keyword', 'schema_type', 'canonical_url', 'noindex', 'settings', 'views',
        'rating_average', 'rating_count', 'product_type', 'language_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'manage_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_favorite' => 'boolean',
        'is_bestseller' => 'boolean',
        'has_price' => 'boolean',
        'noindex' => 'boolean',
        'gallery' => 'array',
        'dimensions' => 'array',
        'badges' => 'array',
        'settings' => 'array',
        'rating_average' => 'decimal:2',
    ];

    protected $attributes = [
        'has_price' => true,
        'stock_quantity' => 0,
        'manage_stock' => false,
        'stock_status' => 'in_stock',
        'is_featured' => false,
        'is_favorite' => false,
        'is_bestseller' => false,
        'noindex' => false,
        'views' => 0,
        'rating_average' => 0.00,
        'rating_count' => 0,
        'product_type' => 'simple',
        'status' => 'draft',
    ];

    // Multi-site: Hide project_id since each project has separate database
    protected $hidden = [
        'project_id',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProjectProductCategory::class, 'product_category_id');
    }

    // Many-to-many relationship for multiple categories
    public function categories()
    {
        return $this->belongsToMany(ProjectProductCategory::class, 'product_category_product', 'product_id', 'product_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(ProjectBrand::class, 'brand_id');
    }

    // Many-to-many relationship for multiple brands
    public function brands()
    {
        return $this->belongsToMany(ProjectBrand::class, 'brand_product', 'product_id', 'brand_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    // Accessors
    public function getDisplayPriceAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return number_format($this->sale_price, 0, ',', '.').' ₫';
        }

        if ($this->price) {
            return number_format($this->price, 0, ',', '.').' ₫';
        }

        return 'Liên hệ';
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
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
