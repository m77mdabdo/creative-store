<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Notifications\RegisteredNotify;

class SendRegisteredNotification
{
    public function handle(Registered $event): void
    {
        $event->user->notify(new RegisteredNotify());
    }
}
