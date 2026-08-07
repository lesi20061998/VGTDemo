<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;

class Menu extends Model
{
    use BelongsToTenant, ProjectScoped;
    
    protected $fillable = ['name', 'slug', 'location', 'is_active', 'tenant_id'];

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }
    
    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }
}
