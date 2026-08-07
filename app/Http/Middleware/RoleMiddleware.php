<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage in routes: ->middleware('role:admin')
     *                  ->middleware('role:admin,hr')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $employee = $request->user();

        if (!$employee) {
            return redirect()->route('login');
        }

        if (!$employee->hasAnyRole($roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
