<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTicket extends Model
{
    protected $fillable = [
        'project_id', 'created_by', 'assigned_to', 'ticket_number', 
        'title', 'description', 'type', 'priority', 'status', 
        'resolution', 'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = 'TK-' . strtoupper(uniqid());
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}

