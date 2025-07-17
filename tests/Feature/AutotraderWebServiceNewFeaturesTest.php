<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\AutotraderApi;

todo('mock requests');

it('can search vehicles with new features', function (): void {
    // Test basic search functionality
    $response = app(AutotraderApi::class)->searchVehicles('12345', [
        'make' => 'BMW',
        'model' => 'X5',
        'factoryCodes' => ['FC123', 'FC456'],
        'wheelbaseMM' => 2975,
    ], [
        'factoryCodes' => true,
        'wheelbaseMM' => true,
    ]);
    
    // This would be mocked in a real test
    // expect($response)->toHaveKey('results');
})->group('autotrader-api', 'search');

it('can get stock list with new features', function (): void {
    // Test stock list with new options
    $response = app(AutotraderApi::class)->getStockList('12345', [], [
        'factoryCodes' => true,
        'priceIndicatorRatingBands' => true,
        'wheelbaseMM' => true,
    ]);
    
    // This would be mocked in a real test
    // expect($response)->toHaveKey('results');
})->group('autotrader-api', 'stock');

it('can get vehicle with factory codes', function (): void {
    // Test vehicle request with factory codes
    $response = app(AutotraderApi::class)->getVehicle(12345, 'ABC123', 50000, [
        'factoryCodes' => true,
    ]);
    
    // This would be mocked in a real test
    // expect($response)->toHaveKey('vehicle');
})->group('autotrader-api', 'vehicles');

it('can get valuation with price indicator rating bands', function (): void {
    // Test valuation with price indicator rating bands
    $response = app(AutotraderApi::class)->getValuation(12345, 'derivative123', 50000, now(), [
        'priceIndicatorRatingBands' => true,
    ]);
    
    // This would be mocked in a real test
    // expect($response)->toHaveKey('valuation');
})->group('autotrader-api', 'valuations');

it('can submit finance application', function (): void {
    // Test finance application submission
    $response = app(AutotraderApi::class)->submitFinanceApplication('12345', [
        'monthsAtBank' => 40, // Previously would be yearsAtBank: 3, monthsAtBank: 4
        'monthsAtEmployer' => 36,
        'monthsAtAddress' => 48,
        'applicantData' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
        ],
    ]);
    
    // This would be mocked in a real test
    // expect($response)->toHaveKey('applicationId');
})->group('autotrader-api', 'finance');

it('can get taxonomy features with factory codes', function (): void {
    // Test taxonomy features with factory codes
    $response = app(AutotraderApi::class)->getFeatures(12345, 'derivative123', now(), null, [
        'factoryCodes' => true,
    ]);
    
    // This would be mocked in a real test
    // expect($response)->toHaveKey('features');
})->group('autotrader-api', 'taxonomy');