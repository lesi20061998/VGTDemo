<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use BelongsToTenant, HasFactory, ProjectScoped, Translatable, SoftDeletes;

    // Các field có thể dịch
    protected $translatable = [
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
    ];

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
        'language',
        'meta_data',
    ];

    protected $casts = [
        'seo_data' => 'array',
        'meta_data' => 'array',
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

    // public function tags(): BelongsToMany
    // {
    //     return $this->belongsToMany(Tag::class, 'post_tag');
    // }

    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->withPivot('order');
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

    public function getNameAttribute(): string
    {
        return $this->title ?? '';
    }

    public function getSkuAttribute(): ?string
    {
        return $this->meta_data['sku'] ?? null;
    }

    public function getShortDescriptionAttribute(): ?string
    {
        return $this->excerpt;
    }

    public function getPriceAttribute()
    {
        return $this->meta_data['price'] ?? 0;
    }

    public function getSalePriceAttribute()
    {
        return $this->meta_data['sale_price'] ?? null;
    }

    public function getDisplayPriceAttribute(): string
    {
        $price = $this->meta_data['sale_price'] ?? $this->meta_data['price'] ?? 0;
        if (! $price) {
            return 'Liên hệ';
        }

        return number_format((float) $price, 0, ',', '.').' đ';
    }

    public function getStockQuantityAttribute(): int
    {
        return (int) ($this->meta_data['stock_quantity'] ?? 0);
    }

    public function getStockStatusAttribute(): string
    {
        if (isset($this->meta_data['stock_status'])) {
            return $this->meta_data['stock_status'];
        }

        return $this->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';
    }

    public function getCategoryAttribute()
    {
        return $this->taxonomies->where('taxonomy', 'product_cat')->first();
    }
}
