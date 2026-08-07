<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brief extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'client_name',
        'requirements',
        'budget',
        'deadline',
        'status',
        'account_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    public function account()
    {
        return $this->belongsTo(User::class, 'account_id');
    }
}
