<?php
// MODIFIED: 2025-01-21

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

/**
 * ProductAttributeValue - Giá trị của thuộc tính
 * 
 * Ví dụ: Color → Red, Blue, Green
 *        Size → S, M, L, XL
 * 
 * Lưu giá trị thực tế cho sản phẩm.
 */
class ProductAttributeValue extends Model
{
    use HasFactory, ProjectScoped;

    protected $fillable = [
        'product_attribute_id', 'value', 'slug', 'display_value', 
        'color_code', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Thuộc tính mà value này thuộc về
     * Ví dụ: Red value thuộc Color attribute
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    /**
     * Các sản phẩm có value này
     * Ví dụ: Sản phẩm nào có màu Red
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_attribute_value_mappings',
            'product_attribute_value_id',
            'product_id'
        );
    }

    // ===== ACCESSORS =====

    public function getDisplayNameAttribute(): string
    {
        return $this->display_value ?: $this->value;
    }
}
