<?php
// MODIFIED: 2025-01-21

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

class OrderItem extends Model
{
    use HasFactory, ProjectScoped;

    protected $fillable = [
        'order_id', 'product_id', 'product_variation_id', 'product_name',
        'product_sku', 'product_attributes', 'unit_price', 'quantity', 'total_price'
    ];

    protected $casts = [
        'product_attributes' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
