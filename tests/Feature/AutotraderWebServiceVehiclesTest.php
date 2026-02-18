<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can request vehicles data', function (): void {

    $token = fake()->uuid;
    $mockVehicleResponse = [
        'vehicle' => [
            'ownershipCondition' => 'Used',
            'registration' => 'DC64AGZ',
            'vin' => 'YV1MV8461F2194079',
            'make' => 'Volvo',
            'model' => 'V40',
            'generation' => 'Hatchback (2012 - 2016)',
            'derivative' => '1.6 D2 R-Design Hatchback 5dr Diesel Manual (88 g/km, 115 bhp)',
            'derivativeId' => '8d0933dd565e328caa7152688f3b18ce',
            'vehicleType' => 'Car',
        ]
    ];

    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Vehicles->value . '*' => Http::response(
            $mockVehicleResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

    expect($response)->toHaveKey('vehicle');
})->group('autotrader-api', 'vehicle');
