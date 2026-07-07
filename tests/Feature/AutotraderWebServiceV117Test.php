<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

describe('Version 1.1.7 API Changes', function () {

    it('returns advertiserVehicleHighlight and priceCommentary fields in the stock list response', function (): void {
        $token = fake()->uuid;
        $mockStockResponse = [
            'results' => [
                [
                    'adverts' => [
                        'retailAdverts' => [
                            'advertiserVehicleHighlight1' => 'Sports Spoiler fitted with livery',
                            'advertiserVehicleHighlight2' => 'Sports Exhaust fitted to increase sporty look from the rear',
                            'advertiserVehicleHighlight3' => 'Fully valeted and serviced with a complete 100 point check completed',
                            'priceCommentary' => '£10000 worth of added extras included on this model',
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
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'*' => Http::response(
                $mockStockResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getStockList(123456);

        $retailAdverts = $response['results'][0]['adverts']['retailAdverts'];

        expect($retailAdverts)->toHaveKeys([
            'advertiserVehicleHighlight1',
            'advertiserVehicleHighlight2',
            'advertiserVehicleHighlight3',
            'priceCommentary',
        ]);
        expect($retailAdverts['advertiserVehicleHighlight1'])->toBe('Sports Spoiler fitted with livery');
        expect($retailAdverts['priceCommentary'])->toBe('£10000 worth of added extras included on this model');
    })->group('autotrader-api', 'stock', 'v1.1.7');

    it('returns advertiserVehicleHighlight and priceCommentary fields in the search response', function (): void {
        $token = fake()->uuid;
        $mockSearchResponse = [
            'results' => [
                [
                    'adverts' => [
                        'retailAdverts' => [
                            'advertiserVehicleHighlight1' => 'Sports Spoiler fitted with livery',
                            'priceCommentary' => '£10000 worth of added extras included on this model',
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

        $response = app(AutotraderApi::class)->searchVehicles(123456, ['make' => 'BMW']);

        $retailAdverts = $response['results'][0]['adverts']['retailAdverts'];

        expect($retailAdverts)->toHaveKey('advertiserVehicleHighlight1');
        expect($retailAdverts)->toHaveKey('priceCommentary');
    })->group('autotrader-api', 'search', 'v1.1.7');

    it('returns priceCommentary and priceCommentaryManufacturerApproved in the advertisers response', function (): void {
        $token = fake()->uuid;
        $mockAdvertisersResponse = [
            'priceCommentary' => 'Good customer service',
            'priceCommentaryManufacturerApproved' => [
                [
                    'make' => 'Ford',
                    'manufacturerApproved' => true,
                    'priceCommentary' => 'Great benefits of being approved include all cars being checked over by Ford technicans and all services completed with Ford approved parts',
                ],
                [
                    'make' => 'Vauxhall',
                    'manufacturerApproved' => false,
                    'priceCommentary' => 'All our cars come with 20 point check and full service history',
                ],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Advertisers->value.'*' => Http::response(
                $mockAdvertisersResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getAdvertisers();

        expect($response)->toHaveKey('priceCommentary');
        expect($response)->toHaveKey('priceCommentaryManufacturerApproved');
        expect($response['priceCommentaryManufacturerApproved'])->toHaveCount(2);
        expect($response['priceCommentaryManufacturerApproved'][0]['make'])->toBe('Ford');
        expect($response['priceCommentaryManufacturerApproved'][0]['manufacturerApproved'])->toBeTrue();
    })->group('autotrader-api', 'advertisers', 'v1.1.7');

})->group('v1.1.7');
