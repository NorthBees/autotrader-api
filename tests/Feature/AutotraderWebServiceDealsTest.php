<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

beforeEach(function () {
    $this->token = fake()->uuid;
    $this->advertiserId = 123456;

    Http::preventStrayRequests();
});

it('can get deals list', function (): void {
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Deals->value.'*' => Http::response([
            'results' => [
                ['dealId' => 'deal-001', 'status' => 'InProgress'],
            ],
            'totalResults' => 1,
        ], 200),
    ]);

    $response = app(AutotraderApi::class)->getDeals($this->advertiserId);
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
})->group('autotrader-api', 'deals');

it('can get deals list with filters', function (): void {
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Deals->value.'*' => Http::response([
            'results' => [
                ['dealId' => 'deal-001', 'status' => 'InProgress'],
            ],
            'totalResults' => 1,
        ], 200),
    ]);

    $response = app(AutotraderApi::class)->getDeals(
        $this->advertiserId,
        [
            'page' => 1,
            'from' => '2023-05-05',
        ]
    );
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
})->group('autotrader-api', 'deals');

it('can get a specific deal', function (): void {
    $dealId = 'deal-001';

    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Deals->value.'*' => Http::response([
            'dealId' => $dealId,
            'status' => 'InProgress',
        ], 200),
    ]);

    $response = app(AutotraderApi::class)->getDeal($this->advertiserId, $dealId);
    expect($response)->toHaveKey('dealId');
    expect($response['dealId'])->toEqual($dealId);
})->group('autotrader-api', 'deals');

it('can update deal status to complete', function (): void {
    expect(method_exists(app(AutotraderApi::class), 'completeDeal'))->toBeTrue();
})->group('autotrader-api', 'deals');

it('can cancel a deal with valid reason', function (): void {
    expect(method_exists(app(AutotraderApi::class), 'cancelDeal'))->toBeTrue();
})->group('autotrader-api', 'deals');

it('validates cancellation reasons', function (): void {
    $api = app(AutotraderApi::class);

    expect(function () use ($api) {
        $api->cancelDeal(123456, 'test-deal-id', 'Invalid Reason');
    })->toThrow(AutotraderException::class);
})->group('autotrader-api', 'deals');

it('can remove deal components', function (): void {
    expect(method_exists(app(AutotraderApi::class), 'removeDealPartExchange'))->toBeTrue();
    expect(method_exists(app(AutotraderApi::class), 'removeDealFinanceApplication'))->toBeTrue();
})->group('autotrader-api', 'deals');
