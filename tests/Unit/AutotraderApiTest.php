<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Traits\AutotraderAuthenticationTrait;
use NorthBees\AutotraderApi\Traits\AutotraderDealsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderFinanceTrait;
use NorthBees\AutotraderApi\Traits\AutotraderIntegrationsTrait;
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

    it('has consistent int advertiserId parameter types across all trait methods', function () {
        $api = new AutotraderApi();
        
        $reflection = new ReflectionClass($api);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        
        // List of methods that should have advertiserId parameter as int
        $methodsWithAdvertiserId = [
            'getCalls', 'getDelivery', 'getDeals', 'getDeal', 'completeDeal', 'cancelDeal', 
            'updateDeal', 'removeDealPartExchange', 'removeDealFinanceApplication', 'createDeal',
            'getMessages', 'markMessagesAsRead', 'sendMessage',
            'submitFinanceApplication', 'getFinanceOptions', 'updateFinanceApplication',
            'generateDescription', 'imageOrder', 'addImage',
            'searchVehicles', 'getSavedSearches', 'saveSearch',
            'getStockList', 'createStock', 'updateStock', 'getStockFeatures', 'getStockSummary',
            'getVehicleTypes', 'getMakes', 'getModels', 'getGenerations', 'getDerivatives', 
            'getFeatures', 'getPrices', 'getTechnicalData', 'getFacets', 'getTaxonomy',
            'getValuation', 'getFutureValuation', 'getHistoricValuation', 'getVehicle', 'getVehicleMetrics'
        ];
        
        foreach ($methods as $method) {
            if (in_array($method->getName(), $methodsWithAdvertiserId)) {
                $parameters = $method->getParameters();
                
                // Find the advertiserId parameter
                $advIdParam = null;
                foreach ($parameters as $param) {
                    if ($param->getName() === 'advertiserId') {
                        $advIdParam = $param;
                        break;
                    }
                }
                
                if ($advIdParam !== null) {
                    $paramType = $advIdParam->getType();
                    expect($paramType)->not()->toBeNull();
                    expect($paramType->getName())->toBe('int', 
                        "Method {$method->getName()} should have int advertiserId parameter, got {$paramType->getName()}");
                }
            }
        }
    });
})->group('main-class', 'autotrader-api');