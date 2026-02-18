<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

it('can request call details', function (): void {

    $token = fake()->uuid;
    $callId = 'ad07cfda-4d02-4068-b7a4-9af6132311a0';
    $advertiserId = 123456;

    $mockCallResponse = [
        'callsId' => $callId,
        'calls' => [
            [
                'at' => '2024-01-15T10:30:00Z',
                'status' => 'completed',
                'hasRecording' => true,
                'durationSeconds' => 120,
            ],
        ],
    ];

    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Calls->value.'*' => Http::response(
            $mockCallResponse,
            200,
            ['content_type' => 'application/json']
        ),
    ]);

    $response = app(AutotraderApi::class)->getCalls($advertiserId, $callId);

    expect($response)->toHaveKey('callsId');
    expect($response['callsId'])->toEqual($callId);
    expect($response)->toHaveKey('calls');
    expect($response['calls'])->toBeArray();

    $firstCall = $response['calls'][0];
    expect($firstCall)->toHaveKey('at');
    expect($firstCall)->toHaveKey('status');
    expect($firstCall)->toHaveKey('hasRecording');
    expect($firstCall)->toHaveKey('durationSeconds');

})->group('autotrader-api', 'calls');
