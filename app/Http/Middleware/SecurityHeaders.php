<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline browser security headers for every response.
 *
 * Content-Security-Policy is sent in two parts:
 * - an enforced policy with the rules that can never break a page
 *   (no plugins, no <base> hijacking, no framing by other sites);
 * - a stricter Report-Only policy listing every origin the store really
 *   uses. Browsers report violations in the console without blocking, so
 *   the remaining inline scripts can be moved out before it is enforced.
 */
class SecurityHeaders
{
    private const ENFORCED_CSP = [
        "object-src 'none'",
        "base-uri 'self'",
        "frame-ancestors 'self'",
    ];

    private const REPORT_ONLY_CSP = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.moyasar.com https://unpkg.com https://maps.googleapis.com https://www.googletagmanager.com",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://unpkg.com https://cdn.moyasar.com",
        "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
        "img-src 'self' data: blob: https:",
        "connect-src 'self' https://api.moyasar.com https://nominatim.openstreetmap.org wss: ws:",
        "frame-src 'self' https://*.moyasar.com https://www.google.com",
        "form-action 'self' https://*.moyasar.com https://accounts.google.com",
        "object-src 'none'",
        "base-uri 'self'",
        "frame-ancestors 'self'",
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=(self)');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        if (! $headers->has('Content-Security-Policy')) {
            $enforced = self::ENFORCED_CSP;

            if ($request->isSecure()) {
                $enforced[] = 'upgrade-insecure-requests';
            }

            $headers->set('Content-Security-Policy', implode('; ', $enforced));
            $headers->set('Content-Security-Policy-Report-Only', implode('; ', self::REPORT_ONLY_CSP));
        }

        if ($request->isSecure() && app()->environment('production')) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $headers->remove('X-Powered-By');

        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
