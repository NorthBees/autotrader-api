<?php

declare(strict_types=1);

todo('mock requests');
it('can request call details', function (): void {

    $callId = 'ad07cfda-4d02-4068-b7a4-9af6132311a0';
    $advertiserId = config('autotrader.default_advertiser_id') ?? 123456;
    
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getCalls($advertiserId, $callId);
    
    expect($response)->toHaveKey('callsId');
    expect($response['callsId'])->toEqual($callId);
    expect($response)->toHaveKey('calls');
    expect($response['calls'])->toBeArray();
    
    // Check first call structure if calls exist
    if (!empty($response['calls'])) {
        $firstCall = $response['calls'][0];
        expect($firstCall)->toHaveKey('at');
        expect($firstCall)->toHaveKey('status');
        expect($firstCall)->toHaveKey('hasRecording');
        expect($firstCall)->toHaveKey('durationSeconds');
    }

})->group('autotrader-api', 'calls');