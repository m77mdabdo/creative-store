<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) abort(403);

        $user = auth()->user();
        foreach ($roles as $role) {
            if ($user->hasRole($role)) return $next($request);
        }

        abort(403);
    }
}
