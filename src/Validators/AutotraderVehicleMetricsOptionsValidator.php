<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Validators;

class AutotraderVehicleMetricsOptionsValidator extends AbstractAutotraderValidator
{
    /**
     * {@inheritDoc}
     */
    protected function getRules(): array
    {
        return [
            'advertiserIdLocations' => 'nullable|array|missing_with:coordinateLocations',
            'advertiserIdLocations.*' => 'required_with:advertiserIdLocations|numeric',

            'coordinateLocations' => 'nullable|array|missing_with:advertiserIdLocations',
            'coordinateLocations.*.latitude' => 'required_with:coordinateLocations|numeric',
            'coordinateLocations.*.longitude' => 'required_with:coordinateLocations|numeric',

            'features' => 'nullable|array',
            'features.*' => 'required_with:features|string',

            'saleTargetDaysInStock' => 'nullable|numeric|min:0|required_with:saleTargetDaysToSell',
            'saleTargetDaysToSell' => 'nullable|array|required_with:saleTargetDaysInStock',
            'saleTargetDaysToSell.*' => 'required_with:saleTargetDaysInStock|numeric|min:0',

            'totalPrice' => 'nullable|numeric|min:0',

            'vatStatus' => 'nullable|string',
        ];
    }
}
