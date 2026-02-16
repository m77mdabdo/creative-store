<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // ✅ حل مشكلة Target class [files] does not exist
        $this->app->singleton('files', function () {
            return new Filesystem();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
