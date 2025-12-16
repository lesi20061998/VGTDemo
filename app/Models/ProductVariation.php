<?php
// MODIFIED: 2025-01-21

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

class ProductVariation extends Model
{
    use HasFactory, ProjectScoped;

    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 'stock_quantity', 
        'attributes', 'is_active'
    ];

    protected $casts = [
        'attributes' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getDisplayPriceAttribute()
    {
        return $this->sale_price ?: $this->price;
    }

    public function getAttributeNamesAttribute()
    {
        if (!$this->attributes) return '';
        
        $names = [];
        foreach ($this->attributes as $attrId => $valueId) {
            $attribute = ProductAttribute::find($attrId);
            $value = ProductAttributeValue::find($valueId);
            if ($attribute && $value) {
                $names[] = $attribute->name . ': ' . $value->display_name;
            }
        }
        return implode(', ', $names);
    }
}
