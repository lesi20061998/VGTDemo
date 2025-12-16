<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id', 'code', 'name', 'email', 'phone', 'position', 'role', 'department', 'superadmin_role', 'is_department_manager', 'department_role', 'manager_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_department_manager' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function isTeamLead()
    {
        return $this->position === 'team_lead' || $this->position === 'manager';
    }

    public function getFullRoleAttribute()
    {
        $positionMap = [
            'staff' => 'Nhân viên',
            'team_lead' => 'Trưởng nhóm',
            'manager' => 'Quản lý'
        ];
        $position = $positionMap[$this->position] ?? $this->position;
        return $this->role ? "{$position} - {$this->role}" : $position;
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members', 'employee_id', 'project_id');
    }

    public function departmentSubordinates()
    {
        return $this->hasMany(Employee::class, 'department', 'department')
            ->where('id', '!=', $this->id);
    }
}

