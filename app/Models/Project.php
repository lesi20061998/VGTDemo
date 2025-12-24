<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'name', 'code', 'subdomain', 'remote_url', 'api_token', 'client_name', 'start_date', 'deadline', 'status', 'contract_value', 'contract_file', 'technical_requirements', 'features', 'environment', 'notes', 'admin_id', 'employee_ids', 'created_by', 'project_admin_username', 'project_admin_password', 'project_admin_password_plain', 'password_updated_at', 'password_updated_by', 'approved_at', 'initialized_at'];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'contract_value' => 'decimal:2',
        'approved_at' => 'datetime',
        'initialized_at' => 'datetime',
        'password_updated_at' => 'datetime',
        'employee_ids' => 'array',
    ];

    protected $hidden = ['project_admin_password', 'project_admin_password_plain'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function admin()
    {
        return $this->belongsTo(Employee::class, 'admin_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function passwordUpdatedBy()
    {
        return $this->belongsTo(User::class, 'password_updated_by');
    }

    public function passwordAudits()
    {
        return $this->hasMany(ProjectPasswordAudit::class);
    }

    public static function generateSubdomain($employeeCode, $contractCode)
    {
        return strtoupper($employeeCode).'.domain.com/'.$contractCode;
    }

    public static function generateProjectAdminPassword()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';

        return substr(str_shuffle(str_repeat($chars, 12)), 0, 12);
    }

    public function members()
    {
        return $this->belongsToMany(Employee::class, 'project_members')->withPivot('role')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function permissions()
    {
        return $this->hasMany(ProjectPermission::class);
    }

    public function hasPermission($module, $action = 'view')
    {
        $permission = $this->permissions()->where('module', $module)->first();

        if (! $permission) {
            return false;
        }

        return $permission->{'can_'.$action} ?? false;
    }

    public function employees()
    {
        if (! $this->employee_ids) {
            return collect([]);
        }

        return Employee::whereIn('id', $this->employee_ids)->get();
    }

    public function hasEmployee($employeeId)
    {
        return $this->employee_ids && in_array($employeeId, $this->employee_ids);
    }

    /**
     * Get the decrypted plain password
     */
    public function getDecryptedPassword(): ?string
    {
        if (!$this->project_admin_password_plain) {
            return null;
        }

        try {
            return decrypt($this->project_admin_password_plain);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt password for project ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Set the encrypted plain password
     */
    public function setEncryptedPassword(string $password): void
    {
        $this->project_admin_password_plain = encrypt($password);
    }
}
