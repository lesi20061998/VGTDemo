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
        return $this->belongsToMany(Role::class, 'user_roles');
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
}
