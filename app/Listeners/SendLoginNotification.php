<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Notifications\LoginNotify;

class SendLoginNotification
{
    public function handle(Login $event): void
    {
        // The Login event fires for both a normal login and Auth::login after Google sign-in
        $driver = auth()->getDefaultDriver(); // Usually "web"
        $event->user->notify(new LoginNotify($driver));
    }
}
