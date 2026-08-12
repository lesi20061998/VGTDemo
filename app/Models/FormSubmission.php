<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\ProjectScoped;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use BelongsToTenant, ProjectScoped;

    protected $fillable = ['form_name', 'data', 'ip_address', 'user_agent', 'status', 'admin_note', 'tenant_id', 'project_id'];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
