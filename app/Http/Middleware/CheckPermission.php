<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->is_active) {
            abort(403, 'Your account is inactive.');
        }

        $permissions = collect($permissions)
            ->flatMap(fn ($item) => explode(',', (string) $item))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        if (empty($permissions)) {
            return $next($request);
        }

        if ($user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to perform this action.');
    }
}
