<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use BelongsToTenant, HasFactory, ProjectScoped;

    protected $fillable = [
        'product_id', 'reviewer_name', 'reviewer_email', 'rating',
        'comment', 'status', 'is_verified', 'tenant_id',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeApproved(Builder $query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighestRated(Builder $query)
    {
        return $query->orderBy('rating', 'desc');
    }
}
