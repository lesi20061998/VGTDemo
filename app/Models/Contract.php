<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = ['employee_id', 'contract_code', 'full_code', 'start_date', 'end_date', 'notes', 'is_active', 'status', 'client_name', 'service_type', 'requirements', 'design_description', 'attachments', 'deadline'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}

