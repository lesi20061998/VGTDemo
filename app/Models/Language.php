<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'locale',
        'flag',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function projectProducts()
    {
        return $this->hasMany(ProjectProduct::class);
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    public static function getActive()
    {
        return static::active()->ordered()->get();
    }

    public static function getByCode($code)
    {
        return static::where('code', $code)->first();
    }
}
