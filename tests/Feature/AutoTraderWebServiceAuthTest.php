<?php

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutoTraderEndpoints;

it('can authenticate with Auto Trader', function () {

    $token = fake()->uuid;
    Http::preventStrayRequests();
    Http::fake([
        AutoTraderEndpoints::ProductionUrl->value.'/*',
        AutoTraderEndpoints::SandboxUrl->value.'/'.AutoTraderEndpoints::Authenticate->value => Http::response(
            [
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200, ['content_type' => 'application/json']),
    ]);

    $response = app(AutotraderApi::class)->getAuthenticationCode();
    expect($response)->toBe($token);
})->group('autotrader-api', 'auth');
