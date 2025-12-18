<?php

// MODIFIED: 2025-01-25 - Added Multi-Tenant Support

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use BelongsToTenant, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'phone',
        'address',
        'status',
        'role',
        'level',
        'project_ids',
        'tenant_id',
        'last_login_at',
        'preferences',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

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
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Methods
    public function hasRole(string|array $roles): bool
    {
        if (\is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin có tất cả permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

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
    public function givePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission && ! $this->permissions->contains($permission)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke permission from user.
     */
    public function revokePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
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

    public function assignRole(string $role): void
    {
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel && ! $this->roles->contains($roleModel)) {
            $this->roles()->attach($roleModel);
        }
    }

    // Accessors
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole(['admin', 'editor', 'support']);
    }

    public function isSuperAdmin(): bool
    {
        return isset($this->level) && $this->level === 0;
    }

    public function isAdministrator(): bool
    {
        return isset($this->level) && $this->level === 1;
    }

    public function canAccessSuperAdmin(): bool
    {
        return isset($this->level) && \in_array($this->level, [0, 1]);
    }

    public function hasAccessToProject(int $projectId): bool
    {
        return $this->project_ids && \in_array($projectId, $this->project_ids);
    }

    public function assignToProject(int $projectId): void
    {
        $projects = $this->project_ids ?? [];
        if (! \in_array($projectId, $projects)) {
            $projects[] = $projectId;
            $this->update(['project_ids' => $projects]);
        }
    }
}
