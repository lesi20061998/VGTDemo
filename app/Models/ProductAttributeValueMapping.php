<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

/**
 * ProductAttributeValueMapping - Kết nối sản phẩm với giá trị thuộc tính
 * 
 * Ví dụ:
 * - Product Áo Đỏ + Color attribute + Red value
 * - Product Áo Đỏ + Size attribute + M value
 * - Product Áo Đỏ + Size attribute + L value
 * 
 * Cho phép 1 sản phẩm có nhiều giá trị của cùng 1 thuộc tính
 * (ví dụ: 1 sản phẩm có màu Red và Blue)
 */
class ProductAttributeValueMapping extends Model
{
    use HasFactory, ProjectScoped;

    protected $table = 'product_attribute_value_mappings';

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ];

    // ===== RELATIONSHIPS =====

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }
}

