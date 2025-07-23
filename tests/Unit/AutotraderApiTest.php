<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Traits\AutotraderAuthenticationTrait;
use NorthBees\AutotraderApi\Traits\AutotraderFinanceTrait;
use NorthBees\AutotraderApi\Traits\AutotraderStockTrait;
use NorthBees\AutotraderApi\Traits\AutotraderTaxonomyTrait;
use NorthBees\AutotraderApi\Traits\AutotraderValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderVehiclesTrait;

describe('AutotraderApi class', function () {
    it('can be instantiated', function () {
        $api = new AutotraderApi();
        
        expect($api)->toBeInstanceOf(AutotraderApi::class);
    });

    it('uses expected traits', function () {
        $api = new AutotraderApi();
        
        expect($api)->toBeInstanceOf(AutotraderApi::class);
        
        // Verify that the class uses the expected traits by checking for trait methods
        $methods = get_class_methods($api);
        
        // From AutotraderAuthenticationTrait
        expect($methods)->toContain('getAuthenticationCode');
    });

    it('has proper endpoint configuration', function () {
        $api = new AutotraderApi();
        
        // Test the protected getEndpoint method through reflection
        $reflection = new ReflectionClass($api);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);
        
        $endpoint = $method->invoke($api);
        
        // Should return sandbox URL by default in tests
        expect($endpoint)->toBe('https://api-sandbox.autotrader.co.uk');
    });
})->group('main-class', 'autotrader-api');