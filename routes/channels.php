<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Personal channel used by the storefront for order updates.
Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
