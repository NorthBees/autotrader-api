<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

describe('Version 1.0 API Changes', function () {

    it('can create a deal', function (): void {
        $token = fake()->uuid;
        $mockDealResponse = [
            'dealId' => 'new-deal-123',
            'stockId' => 'stock-456',
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Deals->value.'*' => Http::response(
                $mockDealResponse,
                201,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->createDeal(123456, [
            'stockId' => 'stock-456',
        ]);

        expect($response)->toHaveKey('dealId');
        expect($response['dealId'])->toBe('new-deal-123');
    })->group('autotrader-api', 'deals', 'v1.0');

    it('can get stock summary', function (): void {
        $token = fake()->uuid;
        $mockSummaryResponse = [
            'stockId' => 'stock-123',
            'lifecycleState' => 'FORECOURT',
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'/*' => Http::response(
                $mockSummaryResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getStockSummary(123456, 'stock-123');

        expect($response)->toHaveKey('stockId');
        expect($response['stockId'])->toBe('stock-123');
    })->group('autotrader-api', 'stock', 'v1.0');

    it('can search vehicles with financeOffers option', function (): void {
        $token = fake()->uuid;
        $mockSearchResponse = [
            'results' => [
                [
                    'id' => 1,
                    'make' => 'BMW',
                    'financeOffers' => [
                        'headlineOffer' => [
                            'monthlyPayment' => 299,
                        ],
                    ],
                ],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Search->value.'*' => Http::response(
                $mockSearchResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->searchVehicles(123456, [
            'make' => 'BMW',
        ], [
            'financeOffers' => 'true',
        ]);

        expect($response)->toHaveKey('results');
    })->group('autotrader-api', 'search', 'v1.0');

    it('can search vehicles with monthlyPriceOption', function (): void {
        $token = fake()->uuid;
        $mockSearchResponse = [
            'results' => [
                ['id' => 1, 'make' => 'Ford'],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Search->value.'*' => Http::response(
                $mockSearchResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->searchVehicles(123456, [
            'make' => 'Ford',
            'monthlyPriceOption' => [
                'mileage' => 10000,
                'deposit' => 1000,
                'term' => 48,
            ],
        ]);

        expect($response)->toHaveKey('results');
    })->group('autotrader-api', 'search', 'v1.0');

    it('has getIntegrations method available', function (): void {
        expect(method_exists(app(AutotraderApi::class), 'getIntegrations'))->toBeTrue();
    })->group('autotrader-api', 'integrations', 'v1.0');

    it('has createDeal method available', function (): void {
        expect(method_exists(app(AutotraderApi::class), 'createDeal'))->toBeTrue();
    })->group('autotrader-api', 'deals', 'v1.0');

    it('has getStockSummary method available', function (): void {
        expect(method_exists(app(AutotraderApi::class), 'getStockSummary'))->toBeTrue();
    })->group('autotrader-api', 'stock', 'v1.0');

    it('getDerivatives accepts oemModelCode parameter', function (): void {
        $api = new AutotraderApi;
        $reflection = new ReflectionMethod($api, 'getDerivatives');
        $parameters = $reflection->getParameters();
        $paramNames = array_map(fn ($p) => $p->getName(), $parameters);

        expect($paramNames)->toContain('oemModelCode');
    })->group('autotrader-api', 'taxonomy', 'v1.0');

    it('getVehicleMetrics accepts vatStatus option', function (): void {
        $api = new AutotraderApi;
        $reflection = new ReflectionMethod($api, 'getVehicleMetrics');
        $parameters = $reflection->getParameters();

        $optionsParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'options') {
                $optionsParam = $param;
                break;
            }
        }

        expect($optionsParam)->not()->toBeNull();
        $defaultValue = $optionsParam->getDefaultValue();
        expect($defaultValue)->toHaveKey('vatStatus');
    })->group('autotrader-api', 'vehicle-metrics', 'v1.0');

})->group('v1.0');
