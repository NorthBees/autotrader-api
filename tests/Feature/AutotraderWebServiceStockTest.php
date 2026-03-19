<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can request stock list', function (): void {

    $token = fake()->uuid;
    $mockStockResponse = [
        'results' => [
            [
                'metadata' => [
                    'lifecycleState' => 'FORECOURT',
                ],
            ],
        ],
        'totalResults' => 1,
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

    $response = app(AutotraderApi::class)->getStockList(123456, ['lifecycleState' => 'FORECOURT']);
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
    expect(Arr::get($response, 'results.0.metadata.lifecycleState'))->toEqual('FORECOURT');

})->group('autotrader-api', 'stock');
