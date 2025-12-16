<?php
// MODIFIED: 2025-01-21

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;

class OrderStatusHistory extends Model
{
    use HasFactory, ProjectScoped;

    protected $fillable = [
        'order_id', 'from_status', 'to_status', 'notes', 'user_id'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
