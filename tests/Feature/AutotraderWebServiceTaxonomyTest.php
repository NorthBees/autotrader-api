<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can request taxonomy data', function (): void {
    
    $token = fake()->uuid;
    $mockVehicleTypesResponse = [
        'vehicleTypes' => [
            ['id' => 1, 'name' => 'Car'],
            ['id' => 2, 'name' => 'Van']
        ]
    ];

    $mockMakesResponse = [
        'makes' => [
            ['makeId' => '1', 'name' => 'Audi'],
            ['makeId' => '2', 'name' => 'BMW']
        ]
    ];

    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Taxonomy->value . '*' => Http::response(
            $mockVehicleTypesResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getVehicleTypes(123456);
    expect($response)->toHaveKey('vehicleTypes');

})->group('autotrader-api', 'taxonomy');
