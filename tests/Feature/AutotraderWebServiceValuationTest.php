<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can request valuation', function (): void {
    
    $token = fake()->uuid;
    $mockValuationResponse = [
        'valuations' => [
            'valuation' => [
                'retail' => 5000,
                'trade' => 4000,
                'average' => 4500
            ]
        ]
    ];
    
    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Valuations->value . '*' => Http::response(
            $mockValuationResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getValuation(
        123456,
        '8d0933dd565e328caa7152688f3b18ce',
        85000,
        \Carbon\Carbon::parse('2015-01-30'),
        [
            'totalPrice' => 5900,
        ],
    );
    expect($response)->toHaveKey('valuations');

})->group('autotrader-api', 'valuation');
