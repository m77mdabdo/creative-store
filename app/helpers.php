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
