<?php

namespace NorthBees\AutoTraderApi\Providers;

use Illuminate\Support\ServiceProvider;

class AutoTraderApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/autotrader.php' => config_path('autotrader.php'),
        ]);
        $this->publishes([
            __DIR__ . '/../../resources/public' => public_path('vendor/northbees'),
        ], 'public');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
