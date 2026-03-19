<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

it('only accepts historic dates', function (): void {

    $token = fake()->uuid;
    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::HistoricValuations->value.'*' => Http::response([
            'message' => 'Historic date required',
            'code' => 400,
        ], 400),
    ]);

    $response = app(AutotraderApi::class)->getHistoricValuation(
        123456,
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        Carbon::parse('2015-01-30'),
        now()->addMonth(),
    );
})->throws(AutotraderException::class);

it('can request history valuation', function (): void {

    $token = fake()->uuid;
    $mockHistoricValuationResponse = [
        'historicValuations' => [
            'valuation' => [
                'retail' => 3500,
                'trade' => 2500,
                'average' => 3000,
            ],
        ],
    ];

    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::HistoricValuations->value.'*' => Http::response(
            $mockHistoricValuationResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getHistoricValuation(
        123456,
        '8d0933dd565e328caa7152688f3b18ce',
        80000,
        Carbon::parse('2015-01-30'),
        Carbon::parse('2020-01-30'),
    );
    expect($response)->toHaveKey('historicValuations');

})->group('autotrader-api', 'valuation');
