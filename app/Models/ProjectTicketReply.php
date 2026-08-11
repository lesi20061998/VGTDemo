<?php

namespace App\Models;

use Database\Factories\ProjectTicketReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTicketReply extends Model
{
    /** @use HasFactory<ProjectTicketReplyFactory> */
    protected $connection = 'mysql';

    protected $fillable = [
        'project_ticket_id',
        'user_id',
        'content',
    ];

    public function ticket()
    {
        return $this->belongsTo(ProjectTicket::class, 'project_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
