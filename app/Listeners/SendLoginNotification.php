<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Notifications\LoginNotify;

class SendLoginNotification
{
    public function handle(Login $event): void
    {
        // Login event بيحصل سواء login عادي أو Auth::login بعد Google
        $driver = auth()->getDefaultDriver(); // غالبًا "web"
        $event->user->notify(new LoginNotify($driver));
    }
}
