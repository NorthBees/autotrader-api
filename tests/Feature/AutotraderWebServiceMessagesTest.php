<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

beforeEach(function () {
    $this->token = fake()->uuid;
    $this->advertiserId = 123456;

    Http::preventStrayRequests();
});

it('can get messages', function (): void {
    $messagesId = '4ae0fc61-f92f-4fa6-9cd5-a8f31a2616c7';

    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Messages->value.'*' => Http::response([
            'messagesId' => $messagesId,
            'consumerLastRead' => '2024-01-15T10:30:00Z',
            'consumerLastReadStatus' => 'Read',
            'advertiserLastRead' => '2024-01-15T11:00:00Z',
            'advertiserLastReadStatus' => 'Read',
            'messages' => [
                [
                    'from' => 'consumer',
                    'at' => '2024-01-15T10:30:00Z',
                    'message' => 'Hello, is this vehicle still available?',
                ],
            ],
        ], 200),
    ]);

    $response = app(AutotraderApi::class)->getMessages($this->advertiserId, $messagesId);

    expect($response)->toHaveKey('messagesId')
        ->toHaveKey('consumerLastRead')
        ->toHaveKey('consumerLastReadStatus')
        ->toHaveKey('advertiserLastRead')
        ->toHaveKey('advertiserLastReadStatus')
        ->toHaveKey('messages');

    expect($response['messagesId'])->toEqual($messagesId);
    expect($response['messages'])->toBeArray();

    expect($response['messages'][0])->toHaveKey('from')
        ->toHaveKey('at')
        ->toHaveKey('message');
})->group('autotrader-api', 'messages');

it('can mark messages as read', function (): void {
    $messagesId = '4ae0fc61-f92f-4fa6-9cd5-a8f31a2616c7';

    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Messages->value.'*' => Http::response([
            'advertiserLastReadStatus' => 'Read',
        ], 200),
    ]);

    $response = app(AutotraderApi::class)->markMessagesAsRead($this->advertiserId, $messagesId);

    expect($response)->toBeArray();
})->group('autotrader-api', 'messages');

it('can send a new message with dealId', function (): void {
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Messages->value.'*' => Http::response([
            'messagesId' => 'new-messages-id',
        ], 200),
    ]);

    $messageData = [
        'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04',
        'message' => 'The new message to send',
    ];

    $response = app(AutotraderApi::class)->sendMessage($this->advertiserId, $messageData);

    expect($response)->toBeArray();
})->group('autotrader-api', 'messages');

it('can send a new message with messagesId', function (): void {
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expires_at' => now()->addMonth()->toISOString(),
            'access_token' => $this->token,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Messages->value.'*' => Http::response([
            'messagesId' => 'e00a1a0a-162d-459b-a23a-0f04adcbb111',
        ], 200),
    ]);

    $messageData = [
        'messagesId' => 'e00a1a0a-162d-459b-a23a-0f04adcbb111',
        'message' => 'The new message to send',
    ];

    $response = app(AutotraderApi::class)->sendMessage($this->advertiserId, $messageData);

    expect($response)->toBeArray();
})->group('autotrader-api', 'messages');

it('validates message length', function (): void {
    $longMessage = str_repeat('a', 1501); // Exceeds 1500 character limit

    $messageData = [
        'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04',
        'message' => $longMessage,
    ];

    expect(fn () => app(AutotraderApi::class)->sendMessage($this->advertiserId, $messageData))
        ->toThrow(AutotraderException::class);
})->group('autotrader-api', 'messages');

it('validates required message data', function (): void {
    // Missing message content
    $messageData = [
        'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04',
    ];

    expect(fn () => app(AutotraderApi::class)->sendMessage($this->advertiserId, $messageData))
        ->toThrow(AutotraderException::class);

    // Missing both dealId and messagesId
    $messageData = [
        'message' => 'Test message',
    ];

    expect(fn () => app(AutotraderApi::class)->sendMessage($this->advertiserId, $messageData))
        ->toThrow(AutotraderException::class);
})->group('autotrader-api', 'messages');
