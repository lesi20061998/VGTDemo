<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, ProjectScoped, BelongsToTenant;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'tenant_id',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
