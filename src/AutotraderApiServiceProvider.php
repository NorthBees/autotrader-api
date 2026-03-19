<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi;

use Illuminate\Support\ServiceProvider;

/**
 * This is the service provider for the Autotrader API package.
 */
class AutotraderApiServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/autotrader.php', 'autotrader');

        // Register the service the package provides.
        $this->app->singleton('autotraderapi', fn ($app): AutotraderApi => new AutotraderApi);
    }

    /**
     * Get the services provided by the provider.
     */
    #[\Override]
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
            __DIR__.'/../config/autotrader.php' => config_path('autotrader.php'),
        ], 'autotrader.config');
    }
}
