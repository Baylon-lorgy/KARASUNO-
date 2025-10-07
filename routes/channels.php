<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel for notifications
Broadcast::channel('notifications', function ($user) {
    return auth()->check();
});

// Public channel for watering events
Broadcast::channel('watering-events', function ($user) {
    return true; // Public channel for watering events
});

// Public channel for sensor alerts
Broadcast::channel('sensor-alerts', function ($user) {
    return true; // Public channel for sensor alerts
});