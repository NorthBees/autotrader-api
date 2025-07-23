<?php

declare(strict_types=1);

todo('mock requests');
it('can request delivery details', function (): void {

    $deliveryId = 'ad07cfda-4d02-4068-b7a4-9af6132311a1';
    $advertiserId = config('autotrader.default_advertiser_id') ?? 123456;
    
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getDelivery($advertiserId, $deliveryId);
    
    expect($response)->toHaveKey('deliveryId');
    expect($response['deliveryId'])->toEqual($deliveryId);
    expect($response)->toHaveKey('method');
    expect($response)->toHaveKey('postcode');

})->group('autotrader-api', 'delivery');