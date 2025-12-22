<?php

namespace App\Traits;

trait ProjectScoped
{
    /**
     * Boot the trait
     */
    protected static function bootProjectScoped()
    {
        static::addGlobalScope('project', function ($builder) {
            if (config('app.bypass_project_scope', false)) {
                return;
            }

            // If using project database connection, don't apply project scope
            if (config('database.default') === 'project') {
                return;
            }

            $project = request()->attributes->get('project');
            if ($project && $builder->getModel()->getTable() !== 'users') {
                $builder->where($builder->getModel()->getTable().'.project_id', $project->id);
            }
        });

        // Automatically set project_id when creating
        static::creating(function ($model) {
            $project = request()->attributes->get('project');
            if ($project && ! $model->project_id) {
                $model->project_id = $project->id;
            }
        });
    }

    /**
     * Get the project relationship
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
