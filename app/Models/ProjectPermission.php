<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPermission extends Model
{
    protected $fillable = ['project_id', 'module', 'can_view', 'can_create', 'can_edit', 'can_delete'];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function getDefaultPermissions()
    {
        return [
            'products' => ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            'categories' => ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            'brands' => ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            'orders' => ['can_view' => true, 'can_create' => false, 'can_edit' => true, 'can_delete' => false],
            'posts' => ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            'pages' => ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            'settings' => ['can_view' => true, 'can_create' => false, 'can_edit' => true, 'can_delete' => false],
        ];
    }
}

