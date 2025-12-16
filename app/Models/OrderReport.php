<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReport extends Model
{
    protected $table = 'order_reports';

    protected $fillable = [
        'order_id', 'total_sales', 'total_orders', 'average_order_value',
        'total_customers', 'report_date', 'report_type'
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'report_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

