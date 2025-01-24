<?php

declare(strict_types=1);

todo('mock requests');
it('only accepts future dates', function (): void {

    $response = app(\NorthBees\AutoTraderApi\AutoTraderApi::class)->getFutureValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        now()->subMonth(),
    );
})->throws(\NorthBees\AutoTraderApi\Exceptions\AutoTraderException::class);

it('can request future valuation', function (): void {
    $response = app(\NorthBees\AutoTraderApi\AutoTraderApi::class)->getFutureValuation(
        config('autotrader.default_advertiser_id'),
        '8d0933dd565e328caa7152688f3b18ce',
        90000,
        \Carbon\Carbon::parse('2015-01-30'),
        now()->addMonth(),
    );
    expect($response)->toHaveKey('futureValuations');

})->group('autotrader-api', 'valuation');
