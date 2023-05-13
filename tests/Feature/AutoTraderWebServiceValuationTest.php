<?php

use NorthBees\AutoTraderApi\AutoTraderApi;

todo('mock requests');
it('can request valuation', function () {

    $response = app(AutoTraderApi::class)->getValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        85000,
        \Carbon\Carbon::parse('2015-01-30'), [
            'totalPrice' => 5900,
        ]

    );
    expect($response)->toHaveKey('valuations');

})->group('autotrader-api', 'valuation');
