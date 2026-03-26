<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline browser security headers for every response.
 *
 * The Content-Security-Policy is enforced. Scripts run only when they carry
 * this request's nonce (every <script> tag in the views prints
 * Vite::cspNonce()); 'strict-dynamic' lets those trusted scripts load what
 * they need (analytics tags, Moyasar, maps), and inline event handlers or
 * injected <script> tags are refused. 'unsafe-inline' and https: in
 * script-src are only fallbacks for browsers without nonce support; modern
 * browsers ignore them once a nonce is present.
 */
class SecurityHeaders
{
    /** @return list<string> */
    private static function policy(string $nonce): array
    {
        return [
            "default-src 'self'",
            "script-src 'nonce-{$nonce}' 'strict-dynamic' 'unsafe-inline' https:",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://unpkg.com https://cdn.moyasar.com https://code.ionicframework.com",
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net https://code.ionicframework.com https://cdn.linearicons.com",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://api.moyasar.com https://nominatim.openstreetmap.org https://maps.googleapis.com"
                . ' https://*.google-analytics.com https://*.analytics.google.com https://www.googletagmanager.com'
                . ' https://www.facebook.com https://*.tiktok.com https://*.tiktokw.us https://tr.snapchat.com https://*.sc-static.net wss: ws:',
            "frame-src 'self' https://*.moyasar.com https://www.google.com https://www.googletagmanager.com https://www.facebook.com",
            "form-action 'self' https://*.moyasar.com https://accounts.google.com",
            "object-src 'none'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
        ];
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Generated before the views render so every <script> gets it.
        Vite::useCspNonce();

        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=(self)');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        if (! $headers->has('Content-Security-Policy')) {
            $policy = self::policy((string) Vite::cspNonce());

            if ($request->isSecure()) {
                $policy[] = 'upgrade-insecure-requests';
            }

            $headers->set('Content-Security-Policy', implode('; ', $policy));
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
