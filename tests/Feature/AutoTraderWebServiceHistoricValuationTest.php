<?php

todo('mock requests');

it('only accepts historic dates', function () {

    $response = app(\NorthBees\AutoTraderApi\AutoTraderApi::class)->getFutureValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        now()->addMonth(),
    );
})->throws(\NorthBees\AutoTraderApi\Exceptions\AutoTraderException::class);

it('only accepts historic dates after registration date', function () {

    $response = app(\NorthBees\AutoTraderApi\AutoTraderApi::class)->getFutureValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        \Carbon\Carbon::parse('2015-01-01'),
    );
})->throws(\NorthBees\AutoTraderApi\Exceptions\AutoTraderException::class);

it('can request history valuation', function () {

    $response = app(\NorthBees\AutoTraderApi\AutoTraderApi::class)->getHistoricValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        80000,
        \Carbon\Carbon::parse('2015-01-30'),
        \Carbon\Carbon::parse('2020-01-30'),
    );
    expect($response)->toHaveKey('historicValuations');

})->group('autotrader-api', 'valuation');
