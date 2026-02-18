<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('only accepts future dates', function (): void {

    $token = fake()->uuid;
    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::FutureValuations->value . '*' => Http::response([
            'message' => 'Future date required',
            'code' => 400
        ], 400),
    ]);

    $response = app(AutotraderApi::class)->getFutureValuation(
        123456,
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        now()->subMonth(),
    );
})->throws(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);

it('can request future valuation', function (): void {
    
    $token = fake()->uuid;
    $mockFutureValuationResponse = [
        'futureValuations' => [
            'valuation' => [
                'retail' => 4500,
                'trade' => 3500,
                'average' => 4000
            ]
        ]
    ];

    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::FutureValuations->value . '*' => Http::response(
            $mockFutureValuationResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getFutureValuation(
        123456,
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        now()->addMonth(),
    );
    expect($response)->toHaveKey('futureValuations');

})->group('autotrader-api', 'valuation');
