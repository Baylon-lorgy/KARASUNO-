<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\MongoDbChannel;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Notification::extend(MongoDbChannel::class, function ($app) {
            return new MongoDbChannel();
        });
    }
} 