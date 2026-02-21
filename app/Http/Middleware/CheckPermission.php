<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permissionCode)
    {
        if (!auth()->check()) abort(403);

        $user = auth()->user();

        // Owner = Full access
        if ($user->hasRole('owner')) return $next($request);

        if (!$user->hasPermission($permissionCode)) abort(403);

        return $next($request);
    }
}
