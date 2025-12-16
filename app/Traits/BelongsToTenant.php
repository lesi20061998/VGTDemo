<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // Tự động thêm tenant_id khi tạo mới
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = session('current_tenant_id') ?? config('app.default_tenant_id');
            }
        });

        // Tự động filter theo tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = session('current_tenant_id') ?? config('app.default_tenant_id');
            if ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(Builder $query, $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
