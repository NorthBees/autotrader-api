<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Tests;

use NorthBees\AutotraderApi\AutotraderApiServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            AutotraderApiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup the configuration
        $app['config']->set('autotrader.environment', 'sandbox');
        $app['config']->set('autotrader.key', 'test-key');
        $app['config']->set('autotrader.secret', 'test-secret');
        $app['config']->set('autotrader.default_advertiser_id', 'test-advertiser-id');
    }
}
