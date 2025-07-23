<?php

declare(strict_types=1);

todo('mock requests');
it('can get messages', function (): void {
    $messagesId = '4ae0fc61-f92f-4fa6-9cd5-a8f31a2616c7';
    $advertiserId = config('autotrader.default_advertiser_id');
    
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getMessages($advertiserId, $messagesId);
    
    expect($response)->toHaveKey('messagesId')
        ->toHaveKey('consumerLastRead')
        ->toHaveKey('consumerLastReadStatus')
        ->toHaveKey('advertiserLastRead')
        ->toHaveKey('advertiserLastReadStatus')
        ->toHaveKey('messages');
        
    expect($response['messagesId'])->toEqual($messagesId);
    expect($response['messages'])->toBeArray();
    
    if (!empty($response['messages'])) {
        expect($response['messages'][0])->toHaveKey('from')
            ->toHaveKey('at')
            ->toHaveKey('message');
    }
})->group('autotrader-api', 'messages');

it('can mark messages as read', function (): void {
    $messagesId = '4ae0fc61-f92f-4fa6-9cd5-a8f31a2616c7';
    $advertiserId = config('autotrader.default_advertiser_id');
    
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->markMessagesAsRead($advertiserId, $messagesId);
    
    // A 200 OK response is returned on success
    expect($response)->toBeArray();
})->group('autotrader-api', 'messages');

it('can send a new message with dealId', function (): void {
    $advertiserId = config('autotrader.default_advertiser_id');
    $messageData = [
        'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04',
        'message' => 'The new message to send'
    ];
    
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->sendMessage($advertiserId, $messageData);
    
    // A 200 OK response is returned on success
    expect($response)->toBeArray();
})->group('autotrader-api', 'messages');

it('can send a new message with messagesId', function (): void {
    $advertiserId = config('autotrader.default_advertiser_id');
    $messageData = [
        'messagesId' => 'e00a1a0a-162d-459b-a23a-0f04adcbb111',
        'message' => 'The new message to send'
    ];
    
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->sendMessage($advertiserId, $messageData);
    
    // A 200 OK response is returned on success
    expect($response)->toBeArray();
})->group('autotrader-api', 'messages');

it('validates message length', function (): void {
    $advertiserId = config('autotrader.default_advertiser_id');
    $longMessage = str_repeat('a', 1501); // Exceeds 1500 character limit
    
    $messageData = [
        'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04',
        'message' => $longMessage
    ];
    
    expect(fn() => app(\NorthBees\AutotraderApi\AutotraderApi::class)->sendMessage($advertiserId, $messageData))
        ->toThrow(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);
})->group('autotrader-api', 'messages');

it('validates required message data', function (): void {
    $advertiserId = config('autotrader.default_advertiser_id');
    
    // Missing message content
    $messageData = [
        'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04'
    ];
    
    expect(fn() => app(\NorthBees\AutotraderApi\AutotraderApi::class)->sendMessage($advertiserId, $messageData))
        ->toThrow(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);
        
    // Missing both dealId and messagesId
    $messageData = [
        'message' => 'Test message'
    ];
    
    expect(fn() => app(\NorthBees\AutotraderApi\AutotraderApi::class)->sendMessage($advertiserId, $messageData))
        ->toThrow(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);
})->group('autotrader-api', 'messages');