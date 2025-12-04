<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles   Pipe-separated list of roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (! $request->user()) {
            abort(403, 'Unauthorized');
        }

        $rolesArray = explode('|', $roles);

        if (! $request->user()->hasAnyRole($rolesArray)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
