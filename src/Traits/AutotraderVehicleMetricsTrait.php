<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Validators\AutotraderVehicleMetricsOptionsValidator;

trait AutotraderVehicleMetricsTrait
{
    /**
     * Calls the Vehicle Metrics endpoint
     * see: https://developers.autotrader.co.uk/api#vehicle-metrics-api.
     *
     * @param int $advertiserId
     * @param string $derivativeId
     * @param int $odometerReadingMiles
     * @param Carbon $firstRegistrationDate
     * @param array $options
     *
     * @throws BindingResolutionException
     * @throws ValidationException
     *
     * @return mixed
     */
    public function getVehicleMetrics(
        int $advertiserId,
        string $derivativeId,
        int $odometerReadingMiles,
        Carbon $firstRegistrationDate,
        array $options = [
            'advertiserIdLocations' => null,
            'coordinateLocations' => null,
            'totalPrice' => null,
            'features' => null,
            'saleTargetDaysInStock' => null,
            'saleTargetDaysToSell' => null,
        ],
    ): mixed {
        $formattedOptions = $this->formatVehicleMetricOptions($options);

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::VehicleMetrics->value . '?advertiserId=' . $advertiserId,
            [],
            array_merge(['vehicle' => ['derivativeId' => $derivativeId,
                'odometerReadingMiles' => $odometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'), ]], $formattedOptions),
        );

    }

    /**
     * Validates and formats the options for the vehicle metrics endpoint.
     *
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    protected function formatVehicleMetricOptions(array $options): array
    {
        $validator = new AutotraderVehicleMetricsOptionsValidator();

        $options = $validator->validate($options);

        if (Arr::get($options, 'totalPrice') !== null) {
            Arr::set($options, 'adverts.retailAdverts.price.amountGBP', $options['totalPrice']);
            unset($options['totalPrice']);
        }

        // There can only be one form of location: advertiser id or coordinate
        if (Arr::get($options, 'advertiserIdLocations') !== null) {
            $options['locations'] = array_map(
                function ($location) {
                    return ['advertiserId' => $location];
                },
                $options['advertiserIdLocations'],
            );

            unset($options['advertiserIdLocations']);
        } elseif (Arr::get($options, 'coordinateLocations') !== null) {
            $options['locations'] = array_map(
                function ($location) {
                    return [
                        'latitude' => $location['latitude'],
                        'longitude' => $location['longitude'],
                    ];
                },
                $options['coordinateLocations'],
            );

            unset($options['coordinateLocations']);
        }

        if (Arr::get($options, 'features') !== null) {
            $options['features'] = array_map(
                function ($feature) {
                    return ['name' => $feature];
                },
                $options['features'],
            );
        }

        // Sale target days in stock and days to sell are used together to return confidence of sale ratings
        // for the supplied days to sell based on the current number of days in stock
        // i.e. if days in stock is 10:
        //  - days to sell 5 will return MISSED as it has passed
        //  - days to sell 20 will return the probability of sale within the next 10 days
        if (Arr::get($options, 'saleTargetDaysInStock') !== null) {
            Arr::set($options, 'salesTarget.daysInStock', $options['saleTargetDaysInStock']);

            $options['salesTarget']['targetDaysToSell'] = array_map(
                function ($daysToSell) {
                    return ['daysToSell' => $daysToSell];
                },
                $options['saleTargetDaysToSell'],
            );
        }

        return $options;
    }
}
