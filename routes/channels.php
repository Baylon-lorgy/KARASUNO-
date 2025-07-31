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

Broadcast::routes(['middleware' => ['web', 'auth:sanctum']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('system-logs', function ($user) {
    return true; // Allow all authenticated users to access system logs
});

Broadcast::channel('notifications', function ($user) {
    return true; // Allow all authenticated users to access notifications
});

Broadcast::channel('sensor-data', function ($user) {
    return auth()->check();
}); 

Broadcast::channel('rainwater-system', function () {
    return true;
});