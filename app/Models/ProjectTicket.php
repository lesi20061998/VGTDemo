<?php

namespace App\Models;

use Database\Factories\ProjectTicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTicket extends Model
{
    /** @use HasFactory<ProjectTicketFactory> */
    protected $connection = 'mysql';

    protected $fillable = [
        'project_id',
        'creator_id',
        'title',
        'description',
        'status',
        'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function replies()
    {
        return $this->hasMany(ProjectTicketReply::class);
    }
}
