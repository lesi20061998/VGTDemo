<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ProjectScoped;
use App\Traits\BelongsToTenant;

class FormSubmission extends Model
{
    use ProjectScoped, BelongsToTenant;

    protected $fillable = ['form_name', 'data', 'ip_address', 'user_agent', 'status', 'admin_note', 'tenant_id'];
    
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

