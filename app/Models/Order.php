<?php

// MODIFIED: 2025-01-21

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToTenant, HasFactory, ProjectScoped;

    /**
     * Get the database connection for the model.
     */
    public function getConnectionName()
    {
        // If we're in a project context (project database is set), use project connection
        if (config('database.default') === 'project') {
            return 'project';
        }

        return parent::getConnectionName();
    }

    protected $fillable = [
        'order_number', 'status', 'subtotal', 'tax_amount', 'shipping_amount',
        'discount_amount', 'total_amount', 'currency', 'customer_name',
        'customer_email', 'customer_phone', 'billing_address', 'shipping_address',
        'payment_method', 'payment_status', 'paid_at', 'customer_notes', 'internal_notes', 'tenant_id',
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeSearch(Builder $query, $search)
    {
        return $query->where('order_number', 'like', "%{$search}%")
            ->orWhere('customer_name', 'like', "%{$search}%")
            ->orWhere('customer_email', 'like', "%{$search}%");
    }

    public function scopeFilter(Builder $query, $filters)
    {
        return $query->when($filters['status'] ?? null, function ($query, $status) {
            return $query->where('status', $status);
        })
            ->when($filters['payment_status'] ?? null, function ($query, $status) {
                return $query->where('payment_status', $status);
            })
            ->when($filters['date_from'] ?? null, function ($query, $date) {
                return $query->whereDate('created_at', '>=', $date);
            })
            ->when($filters['date_to'] ?? null, function ($query, $date) {
                return $query->whereDate('created_at', '<=', $date);
            });
    }

    // Methods
    public function updateStatus($newStatus, $notes = null, $userId = null)
    {
        $oldStatus = $this->status;
        $this->update(['status' => $newStatus]);

        $this->statusHistories()->create([
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $notes,
            'user_id' => $userId,
        ]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}
