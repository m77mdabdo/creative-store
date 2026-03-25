<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        if (! auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();

        if ($user->hasRole('owner') || $user->hasRole('admin')) {
            return $next($request);
        }

        if (! $user->hasPermission($permissionCode)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
