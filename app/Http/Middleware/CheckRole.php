<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->is_active) {
            abort(403, 'Your account is inactive.');
        }

        $roles = collect($roles)
            ->flatMap(fn ($item) => explode(',', (string) $item))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        if (empty($roles)) {
            return $next($request);
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'You are not authorized to access this area.');
    }
}
