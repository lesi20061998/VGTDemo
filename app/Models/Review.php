<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use BelongsToTenant, HasFactory, ProjectScoped, SoftDeletes;

    protected $fillable = [
        'project_id',
        'tenant_id',
        'post_id',
        'reviewer_name',
        'reviewer_email',
        'reviewer_avatar',
        'reviewer_title',
        'content',
        'rating',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];

    // =====================
    // Relationships
    // =====================

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // =====================
    // Scopes
    // =====================

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function scopeHighestRated(Builder $query): Builder
    {
        return $query->orderByDesc('rating');
    }
}
