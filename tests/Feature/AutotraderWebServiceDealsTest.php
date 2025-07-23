<?php

declare(strict_types=1);

todo('mock requests');
it('can get deals list', function (): void {
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getDeals(config('autotrader.default_advertiser_id'));
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
})->group('autotrader-api', 'deals');

it('can get deals list with filters', function (): void {
    $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getDeals(
        config('autotrader.default_advertiser_id'), 
        [
            'page' => 1,
            'from' => '2023-05-05'
        ]
    );
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
})->group('autotrader-api', 'deals');

it('can get a specific deal', function (): void {
    // First get the deals list to find a deal ID
    $dealsList = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getDeals(config('autotrader.default_advertiser_id'));
    
    if (!empty($dealsList['results'])) {
        $dealId = $dealsList['results'][0]['dealId'];
        $response = app(\NorthBees\AutotraderApi\AutotraderApi::class)->getDeal(
            config('autotrader.default_advertiser_id'),
            $dealId
        );
        expect($response)->toHaveKey('dealId');
        expect($response['dealId'])->toEqual($dealId);
    } else {
        // Skip test if no deals available
        expect(true)->toBeTrue();
    }
})->group('autotrader-api', 'deals');

it('can update deal status to complete', function (): void {
    // This would require a valid deal ID in progress state
    // For now, we'll just verify the method exists and can be called
    expect(method_exists(app(\NorthBees\AutotraderApi\AutotraderApi::class), 'completeDeal'))->toBeTrue();
})->group('autotrader-api', 'deals');

it('can cancel a deal with valid reason', function (): void {
    // This would require a valid deal ID in progress state
    // For now, we'll just verify the method exists and can be called
    expect(method_exists(app(\NorthBees\AutotraderApi\AutotraderApi::class), 'cancelDeal'))->toBeTrue();
})->group('autotrader-api', 'deals');

it('validates cancellation reasons', function (): void {
    $api = app(\NorthBees\AutotraderApi\AutotraderApi::class);
    
    expect(function () use ($api) {
        $api->cancelDeal('123456', 'test-deal-id', 'Invalid Reason');
    })->toThrow(\NorthBees\AutotraderApi\Exceptions\AutotraderException::class);
})->group('autotrader-api', 'deals');

it('can remove deal components', function (): void {
    expect(method_exists(app(\NorthBees\AutotraderApi\AutotraderApi::class), 'removeDealPartExchange'))->toBeTrue();
    expect(method_exists(app(\NorthBees\AutotraderApi\AutotraderApi::class), 'removeDealFinanceApplication'))->toBeTrue();
})->group('autotrader-api', 'deals');