<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory, BelongsToTenant, ProjectScoped;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'post_type',
        'template',
        'status',
        'meta_title',
        'meta_description',
        'seo_data',
        'views',
        'published_at',
        'author_id',
        'tenant_id',
    ];

    protected $casts = [
        'seo_data' => 'array',
        'published_at' => 'datetime',
    ];

    // Scopes cho post type
    public function scopePosts($query)
    {
        return $query->where('post_type', 'post');
    }

    public function scopePages($query)
    {
        return $query->where('post_type', 'page');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('order');
    }

    // Helper methods
    public function isPost(): bool
    {
        return $this->post_type === 'post';
    }

    public function isPage(): bool
    {
        return $this->post_type === 'page';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
