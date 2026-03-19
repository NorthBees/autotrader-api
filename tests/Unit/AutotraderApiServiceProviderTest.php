<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\AutotraderApiServiceProvider;

describe('AutotraderApiServiceProvider', function () {
    it('registers the autotraderapi service', function () {
        $provider = new AutotraderApiServiceProvider($this->app);
        $provider->register();

        expect($this->app->bound('autotraderapi'))->toBeTrue();
        expect($this->app->make('autotraderapi'))->toBeInstanceOf(AutotraderApi::class);
    });

    it('provides the correct services', function () {
        $provider = new AutotraderApiServiceProvider($this->app);

        expect($provider->provides())->toBe(['autotraderapi']);
    });

    it('merges configuration', function () {
        $provider = new AutotraderApiServiceProvider($this->app);
        $provider->register();

        // After registration, config should be available
        expect(config('autotrader'))->toBeArray();
        expect(config('autotrader.environment'))->toBe('sandbox');
    });

    it('can be instantiated', function () {
        $provider = new AutotraderApiServiceProvider($this->app);

        expect($provider)->toBeInstanceOf(AutotraderApiServiceProvider::class);
        expect($provider)->toBeInstanceOf(ServiceProvider::class);
    });
})->group('service-provider', 'autotrader');
