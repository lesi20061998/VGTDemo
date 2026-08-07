<?php

// MODIFIED: 2025-01-21

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ProductAttribute - Loại thuộc tính chung
 *
 * Ví dụ: Color, Size, Material
 * Là định nghĩa tên, slug, loại (text/select).
 * Có thể áp dụng cho nhiều sản phẩm.
 */
class ProductAttribute extends Model
{
    use HasFactory; // Temporarily disabled ProjectScoped, BelongsToTenant

    protected $fillable = [
        'attribute_group_id', 'name', 'slug', 'type', 'is_filterable',
        'is_required', 'sort_order', 'tenant_id',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_required' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Nhóm thuộc tính mà attribute này thuộc về
     */
    public function group()
    {
        return $this->belongsTo(AttributeGroup::class, 'attribute_group_id');
    }

    /**
     * Danh sách các giá trị có thể cho thuộc tính này
     * Ví dụ: Color attribute → Red, Blue, Green values
     */
    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class)->orderBy('sort_order');
    }

    /**
     * Các sản phẩm sử dụng thuộc tính này
     * Quan hệ many-to-many qua pivot: product_attribute_value_mappings
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_attribute_value_mappings',
            'product_attribute_id',
            'product_id'
        )->distinct();
    }

    // ===== SCOPES =====

    public function scopeFilterable(Builder $query): Builder
    {
        return $query->where('is_filterable', true);
    }

    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_required', true);
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('slug', 'like', "%{$search}%");
    }
}
