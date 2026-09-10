<?php

if (! function_exists('normalize_error_log_level')) {
    function normalize_error_log_level(\Throwable $e): string
    {
        return match (true) {
            $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 'warning',
            $e instanceof \Illuminate\Auth\AuthenticationException => 'warning',
            $e instanceof \Illuminate\Validation\ValidationException => 'warning',
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                && $e->getStatusCode() >= 500 => 'critical',
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                && $e->getStatusCode() >= 400 => 'warning',
            $e instanceof \PDOException => 'critical',
            $e instanceof \Error => 'critical',
            default => 'error',
        };
    }
}

if (! function_exists('versioned_asset')) {
    /**
     * Public asset URL with a cache-busting version based on the file's mtime.
     */
    function versioned_asset(string $path): string
    {
        static $versions = [];

        $path = ltrim($path, '/');

        if (! array_key_exists($path, $versions)) {
            $file = public_path($path);
            $versions[$path] = is_file($file) ? (string) filemtime($file) : null;
        }

        return asset($path) . ($versions[$path] ? '?v=' . $versions[$path] : '');
    }
}

if (! function_exists('media_disk')) {
    /**
     * The filesystem disk that stores publicly served media.
     */
    function media_disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk', 'public'));
    }
}

if (! function_exists('media_url')) {
    /**
     * Public URL for a stored media path; absolute URLs are returned as-is.
     */
    function media_url(?string $path, ?string $fallback = null): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return $fallback;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // Bundled theme assets (e.g. cozastore/images/...) live in public/.
        if (str_starts_with($path, 'storage/') || is_file(public_path($path))) {
            return asset($path);
        }

        $disk = config('filesystems.media_disk', 'public');

        // Local public disk: build the URL from the current request so images
        // load on whatever host and port serves the site, not only APP_URL.
        if (config("filesystems.disks.{$disk}.driver") === 'local') {
            return asset('storage/' . ltrim($path, '/'));
        }

        return media_disk()->url($path);
    }
}

if (! function_exists('status_label')) {
    /**
     * Human label for an enum-like value (status, type, role, channel) in the
     * current language: "partially_refunded" → "Partially Refunded" → "مسترد جزئيًا".
     */
    function status_label(?string $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return __(\Illuminate\Support\Str::headline($value));
    }
}

if (! function_exists('money')) {
    /**
     * Price for display in the current language: "289.00 ر.س" / "289.00 SAR".
     */
    function money(float|int|string|null $amount, ?string $currency = 'SAR'): string
    {
        $currency = $currency ?: 'SAR';
        $label = strtoupper($currency) === 'SAR' ? __('SAR') : $currency;

        return number_format((float) $amount, 2) . ' ' . $label;
    }
}

if (! function_exists('pdf_arabic')) {
    /**
     * Shape Arabic text for dompdf, which renders letters unjoined and in
     * the wrong order. Non-Arabic text is returned unchanged.
     */
    function pdf_arabic(string $text): string
    {
        if ($text === '' || ! preg_match('/\p{Arabic}/u', $text)) {
            return $text;
        }

        static $shaper = null;
        $shaper ??= new \ArPHP\I18N\Arabic();

        return $shaper->utf8Glyphs($text);
    }
}
