<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the broadcasting routes with web middleware first
        Broadcast::routes(['middleware' => ['web']]);

        // Then register the channel authorization routes
        require base_path('routes/channels.php');
    }
} 