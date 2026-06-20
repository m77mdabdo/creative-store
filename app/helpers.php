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

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return media_disk()->url($path);
    }
}
