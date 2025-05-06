<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use NorthBees\AutoTraderApi\Tests\Traits\TestableAutoTraderVehicleMetricsTrait;

$trait = new TestableAutoTraderVehicleMetricsTrait();

describe('AutoTraderVehicleMetricsTrait', function () use ($trait) {
    it("formatVehicleMetricOptions will transform features to the expected format", function () use ($trait) {
        $features = [
            "one",
            "two",
        ];

        $options = ['features' => $features];

        $result = $trait->publicFormatVehicleMetricOptions($options);


        $features = $result['features'];

        expect(Arr::get($features, '0.name'))
            ->toBe('one')
            ->and(Arr::get($features, '1.name'))
            ->toBe('two');
    });

    it("formatVehicleMetricOptions will set the expected key when totalPrice is provided", function () use ($trait) {
        $price = 123;
        $options = ['totalPrice' => $price];

        $result = $trait->publicFormatVehicleMetricOptions($options);


        expect(Arr::get($result, 'adverts.retailAdverts.price.amountGBP'))
            ->toBe($price)
            ->and($result)
            ->not()
            ->toHaveKey('totalPrice');

    });
})->group('autotrader-api', 'vehicle-metrics');
