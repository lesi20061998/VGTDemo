<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if this is a project route
        $projectCode = $request->route('projectCode');

        if ($projectCode) {
            return route('project.login', ['projectCode' => $projectCode]);
        }

        return route('login');
    }

    /**
     * Get the guards that should be used for authentication.
     */
    protected function authenticate($request, array $guards): void
    {
        // If no guards specified, determine based on route
        if (empty($guards)) {
            $projectCode = $request->route('projectCode');
            $guards = $projectCode ? ['project'] : ['web'];
        }

        parent::authenticate($request, $guards);
    }
}
