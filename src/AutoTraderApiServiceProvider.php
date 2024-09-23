<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi;

use Illuminate\Support\ServiceProvider;

class AutoTraderApiServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {

        $this->publishes([
            __DIR__ . '/../resources/public' => public_path('vendor/northbees'),
        ], 'public');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/autotrader.php', 'autotrader');

        // Register the service the package provides.
        $this->app->singleton('autotraderapi', function ($app) {
            return new AutoTraderApi();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['autotraderapi'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/autotrader.php' => config_path('autotrader.php'),
        ], 'autotrader.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/northbees'),
        ], 'autotraderapi.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/northbees'),
        ], 'autotraderapi.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/northbees'),
        ], 'autotraderapi.views');*/

        // Registering package commands.
        $this->commands([
        ]);
    }
}
