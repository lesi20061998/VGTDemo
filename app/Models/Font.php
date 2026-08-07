<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Font extends Model
{
    use BelongsToTenant;

    protected $fillable = ['name', 'type', 'weights', 'load_string', 'file_path', 'is_active', 'is_default', 'tenant_id'];

    protected $casts = [
        'weights' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($font) {
            if ($font->is_default) {
                static::where('id', '!=', $font->id)->update(['is_default' => false]);
            }
        });
    }

    public function getLoadUrlAttribute()
    {
        if ($this->type === 'google') {
            if ($this->load_string) {
                return $this->load_string;
            }
            $weights = implode(';', $this->weights ?? [400]);
            return "https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $this->name) . ":wght@{$weights}&display=swap";
        }
        return asset($this->file_path);
    }
}

