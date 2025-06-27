<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can authenticate with Auto Trader', function (): void {

    $token = fake()->uuid;
    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::ProductionUrl->value . '/*',
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response(
            [
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ],
            200,
            ['content_type' => 'application/json'],
        ),
    ]);

    $response = app(AutotraderApi::class)->getAuthenticationCode();
    expect($response)->toBe($token);
})->group('autotrader-api', 'auth');
