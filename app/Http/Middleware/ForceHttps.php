<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends plain-HTTP visitors to the HTTPS address in production.
 *
 * Runs only when APP_URL itself is https, so local development on
 * http://localhost keeps working. Behind a load balancer this relies on
 * TRUSTED_PROXIES, otherwise every request would look like plain HTTP.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRedirect($request)) {
            return redirect()->to('https://' . $request->getHttpHost() . $request->getRequestUri(), 301);
        }

        return $next($request);
    }

    private function shouldRedirect(Request $request): bool
    {
        return ! $request->isSecure()
            && app()->environment('production')
            && str_starts_with((string) config('app.url'), 'https://')
            && ! $request->is('up');
    }
}
