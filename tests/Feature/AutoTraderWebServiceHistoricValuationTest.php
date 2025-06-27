<?php

declare(strict_types=1);

todo('mock requests');

it('only accepts historic dates', function (): void {

    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getFutureValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        now()->addMonth(),
    );
})->throws(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);

it('only accepts historic dates after registration date', function (): void {

    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getFutureValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        \Carbon\Carbon::parse('2015-01-01'),
    );
})->throws(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);

it('can request history valuation', function (): void {

    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getHistoricValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        80000,
        \Carbon\Carbon::parse('2015-01-30'),
        \Carbon\Carbon::parse('2020-01-30'),
    );
    expect($response)->toHaveKey('historicValuations');

})->group('autotrader-api', 'valuation');
