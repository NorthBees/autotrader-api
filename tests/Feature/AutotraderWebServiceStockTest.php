<?php

declare(strict_types=1);

todo('mock requests');
it('can request stock list', function (): void {

    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getStockList(config('autotrader.default_advertiser_id'), ['lifecycleState' => 'FORECOURT']);
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
    expect(\Illuminate\Support\Arr::get($response, 'results.0.metadata.lifecycleState'))->toEqual('FORECOURT');

    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getStockList(config('autotrader.default_advertiser_id'), ['lifecycleState' => 'WASTEBIN']);

    expect(\Illuminate\Support\Arr::get($response, 'results.0.metadata.lifecycleState'))->toEqual('WASTEBIN');

})->group('autotrader-api', 'stock');
