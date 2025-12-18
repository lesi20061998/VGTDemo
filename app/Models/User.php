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
        'role',
        'level',
        'project_ids',
        'tenant_id',
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
        ];
    }

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Methods
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function can($permission)
    {
        // Super admin có tất cả permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin có hầu hết permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        // Editor có permissions cho content management
        if ($this->hasRole('editor')) {
            $editorPermissions = [
                'manage products',
                'manage categories',
                'manage brands',
                'manage attributes',
                'manage posts',
                'manage pages',
                'manage menus',
            ];

            return in_array($permission, $editorPermissions);
        }

        // Support có permissions hạn chế
        if ($this->hasRole('support')) {
            $supportPermissions = [
                'view products',
                'view categories',
                'view orders',
            ];

            return in_array($permission, $supportPermissions);
        }

        return false;
    }

    public function assignRole($role)
    {
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel && ! $this->roles->contains($roleModel)) {
            $this->roles()->attach($roleModel);
        }
    }

    // Accessors
    public function getIsAdminAttribute()
    {
        return $this->hasRole(['admin', 'editor', 'support']);
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

    public function hasAccessToProject($projectId)
    {
        return $this->project_ids && in_array($projectId, $this->project_ids);
    }

    public function assignToProject($projectId)
    {
        $projects = $this->project_ids ?? [];
        if (! in_array($projectId, $projects)) {
            $projects[] = $projectId;
            $this->update(['project_ids' => $projects]);
        }
    }
}
