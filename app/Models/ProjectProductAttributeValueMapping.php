<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ProjectProductAttributeValueMapping - Kết nối sản phẩm với giá trị thuộc tính trong project database
 *
 * Ví dụ:
 * - Product ID 1 có Color = Red (attribute_id=1, value_id=5)
 * - Product ID 1 có Size = M (attribute_id=2, value_id=8)
 *
 * Bảng này cho phép 1 sản phẩm có nhiều giá trị cho cùng 1 thuộc tính
 * (ví dụ: 1 sản phẩm có màu Red và Blue)
 */
class ProjectProductAttributeValueMapping extends Model
{
    use HasFactory;

    protected $connection = 'project';

    protected $table = 'product_attribute_value_mappings';

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Sản phẩm mà mapping này thuộc về
     */
    public function product()
    {
        return $this->belongsTo(ProjectProduct::class, 'product_id');
    }

    /**
     * Thuộc tính (loại) - ví dụ: Color, Size
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    /**
     * Giá trị cụ thể - ví dụ: Red, Blue, M, L
     */
    public function attributeValue()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }
}
