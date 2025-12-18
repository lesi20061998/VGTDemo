<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ProjectUser extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Sử dụng connection project (được set bởi SetProjectDatabase middleware)
     */
    protected $connection = 'project';

    /**
     * Tên bảng trong database
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'level',
        'project_ids',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'role' => 'cms',
        'level' => 2,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'project_ids' => 'array',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(ProjectRole::class, 'user_roles', 'user_id', 'role_id');
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function isSuperAdmin()
    {
        return isset($this->level) && $this->level === 0;
    }

    public function isAdministrator()
    {
        return isset($this->level) && $this->level === 1;
    }

    public function canAccessSuperAdmin()
    {
        return isset($this->level) && in_array($this->level, [0, 1]);
    }

    public function permissions()
    {
        return $this->belongsToMany(ProjectPermission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin có tất cả permissions
        if ($this->isSuperAdmin()) {
            return true;
        }
        // dd($permission);
        // Check direct permissions
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Give permission directly to user.
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
     * Revoke permission from user.
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

    /**
     * Get all permissions for user (direct + through roles).
     */
    public function getAllPermissions()
    {
        $directPermissions = $this->permissions;
        $rolePermissions = $this->roles->flatMap->permissions;

        return $directPermissions->merge($rolePermissions)->unique('id');
    }
}
