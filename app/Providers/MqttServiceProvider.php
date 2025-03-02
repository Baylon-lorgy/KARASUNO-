<?php

namespace App\Providers;

use App\Services\MqttService;
use Illuminate\Support\ServiceProvider;

class MqttServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MqttService::class, function ($app) {
            return new MqttService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 