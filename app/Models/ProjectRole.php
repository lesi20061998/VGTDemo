<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectRole extends Model
{
    use HasFactory;

    protected $connection = 'project';

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_default',
        'level',
        'permissions',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'permissions' => 'array',
    ];

    /**
     * Get the users that have this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(ProjectUser::class, 'user_roles', 'role_id', 'user_id');
    }

    /**
     * Get the permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(ProjectPermission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Give permission to role.
     */
    public function givePermissionTo(string|ProjectPermission $permission): void
    {
        if (is_string($permission)) {
            $permission = ProjectPermission::where('name', $permission)->first();
        }

        if ($permission && ! $this->permissions->contains($permission)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermissionTo(string|ProjectPermission $permission): void
    {
        if (is_string($permission)) {
            $permission = ProjectPermission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission);
        }
    }
}
