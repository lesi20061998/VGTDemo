<?php

namespace App\Traits;

use App\Models\Project;

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

            // Always apply project scope when a project is in the request context.
            // NOTE: We intentionally removed the bypass for 'project' DB connection because
            // this system uses shared DB mode where 'project' connection = same DB as 'mysql'.
            // Scoping must always apply to prevent cross-site data leaks.
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
        return $this->belongsTo(Project::class);
    }
}
