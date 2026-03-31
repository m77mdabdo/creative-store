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

if (! function_exists('format_backup_size')) {
    function format_backup_size($bytes): string
    {
        if (is_null($bytes) || ! is_numeric($bytes)) {
            return '—';
        }

        $bytes = (float) $bytes;

        if ($bytes < 1024) {
            return number_format($bytes, 0) . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        if ($bytes < 1024 * 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2) . ' MB';
        }

        return number_format($bytes / (1024 * 1024 * 1024), 2) . ' GB';
    }
}
