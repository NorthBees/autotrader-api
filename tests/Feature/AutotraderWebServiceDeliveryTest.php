<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can request delivery details', function (): void {

    $token = fake()->uuid;
    $deliveryId = 'ad07cfda-4d02-4068-b7a4-9af6132311a1';
    $advertiserId = 123456;

    $mockDeliveryResponse = [
        'deliveryId' => $deliveryId,
        'method' => 'collection',
        'postcode' => 'SW1A 1AA',
    ];

    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value . '/' . AutotraderEndpoints::Delivery->value . '*' => Http::response(
            $mockDeliveryResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getDelivery($advertiserId, $deliveryId);

    expect($response)->toHaveKey('deliveryId');
    expect($response['deliveryId'])->toEqual($deliveryId);
    expect($response)->toHaveKey('method');
    expect($response)->toHaveKey('postcode');

})->group('autotrader-api', 'delivery');
