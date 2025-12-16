<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use Spatie\Translatable\HasTranslations;

class Branch extends Model
{
    use HasTranslations, ProjectScoped;

    protected $fillable = ['tenant_id', 'name', 'address', 'map_embed'];

    public $translatable = ['name'];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            if (session('current_tenant_id')) {
                $query->where('tenant_id', session('current_tenant_id'));
            }
        });

        static::creating(function ($branch) {
            if (!$branch->tenant_id && session('current_tenant_id')) {
                $branch->tenant_id = session('current_tenant_id');
            }
        });
    }
}

