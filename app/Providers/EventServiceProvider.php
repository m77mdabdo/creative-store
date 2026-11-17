<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use App\Listeners\SendRegisteredNotification;
use App\Listeners\SendLoginNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendRegisteredNotification::class,
        ],
        Login::class => [
            SendLoginNotification::class,
        ],
    ];

    // Disable automatic event discovery (it needs 'files')
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
