<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can search vehicles with new features', function (): void {

    $token = fake()->uuid;
    $mockSearchResponse = [
        'results' => [
            ['id' => 1, 'make' => 'BMW', 'model' => 'X5'],
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
        'model' => 'X5',
    ]);

    expect($response)->toHaveKey('results');
})->group('autotrader-api', 'search');

it('can get stock list with new features', function (): void {

    $token = fake()->uuid;
    $mockStockResponse = [
        'results' => [
            ['id' => 1, 'metadata' => ['lifecycleState' => 'FORECOURT']],
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

    $response = app(AutotraderApi::class)->getStockList(123456, []);

    expect($response)->toHaveKey('results');
})->group('autotrader-api', 'stock');
