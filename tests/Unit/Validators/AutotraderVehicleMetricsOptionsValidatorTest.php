<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\Validators\AutotraderVehicleMetricsOptionsValidator;

describe('AutotraderVehicleMetricsOptionsValidator', function () {
    it("should return an empty array when the input is empty", function () {
        $validator = app(AutotraderVehicleMetricsOptionsValidator::class);

        $result = $validator->validate([]);

        expect($result)->toBe([]);
    });

    it("should return the validated array when the input is valid", function () {
        $validator = app(AutotraderVehicleMetricsOptionsValidator::class);

        $dataValidations = collect([
            // with advertiserIdLocations
            [
                'advertiserIdLocations' => [1, 2, 3],
                'features' => ['feature1', 'feature2', 'feature3'],
                'saleTargetDaysInStock' => 1,
                'saleTargetDaysToSell' => [1, 2, 3],
                'totalPrice' => 1,
            ],
            // with coordinateLocations
            [
                'coordinateLocations' => [
                    ['latitude' => 1, 'longitude' => 1],
                    ['latitude' => 2, 'longitude' => 2],
                    ['latitude' => 3, 'longitude' => 3],
                ],
                'features' => ['feature1', 'feature2', 'feature3'],
                'saleTargetDaysInStock' => 1,
                'saleTargetDaysToSell' => [1, 2, 3],
                'totalPrice' => 1,
            ],
        ]);

        // the result should be the same as the input as there are no extra fields etc.
        $dataValidations->each(function ($data) use ($validator) {
            $result = $validator->validate($data);

            expect($result)->toMatchArray($data);
        });
    });

    it("should return the validated array with any unexpected fields removed", function () {
        $validator = app(AutotraderVehicleMetricsOptionsValidator::class);

        $data = [
            'advertiserIdLocations' => [1, 2, 3],
            'features' => ['feature1', 'feature2', 'feature3'],
            'saleTargetDaysInStock' => 1,
            'saleTargetDaysToSell' => [1, 2, 3],
            'totalPrice' => 1,
            'unexpectedField' => 'unexpectedValue',
        ];

        $result = $validator->validate($data);

        expect($result)->not->toHaveKey('unexpectedField');
    });

    it("should throw a validation exception when advertiserIdLocations and coordinateLocations are both set", function () {
        $validator = app(AutotraderVehicleMetricsOptionsValidator::class);

        $data = [
            'advertiserIdLocations' => [1, 2, 3],
            'coordinateLocations' => [
                ['latitude' => 1, 'longitude' => 1],
                ['latitude' => 2, 'longitude' => 2],
                ['latitude' => 3, 'longitude' => 3],
            ],
        ];

        $this->expectException(ValidationException::class);
        $validator->validate($data);
    });

    it("should throw a validation exception when saleTargetDaysInStock is set without saleTargetDaysToSell", function () {
        $validator = app(AutotraderVehicleMetricsOptionsValidator::class);

        $data = [
            'saleTargetDaysInStock' => 1,
        ];

        $this->expectException(ValidationException::class);
        $validator->validate($data);
    });
})->group('validator', 'vehicle-metrics');
