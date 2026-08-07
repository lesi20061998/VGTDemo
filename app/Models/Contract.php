<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'client_name',
        'service_type',
        'start_date',
        'end_date',
        'domain_name',
        'domain_purchase_date',
        'hosting_provider',
        'hosting_start_date',
        'contract_value',
        'attachments',
        'status',
        'description',
        'technical_requirements',
        'features',
        'has_client_resources',
        'client_resource_details',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'domain_purchase_date' => 'datetime',
        'hosting_start_date' => 'datetime',
        'contract_value' => 'decimal:2',
        'attachments' => 'array',
        'has_client_resources' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
