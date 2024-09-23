<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;

trait AutoTraderVehicleMetricsTrait
{
    public function getVehicleMetrics(int $advertiserId, string $derivativeId, int $odometerReadingMiles, Carbon $firstRegistrationDate, array $options = [
        'location' => null,
        'totalPrice' => null,
        'features' => null,
        'conditionRating' => null,
        'advertiserId' => null,
    ])
    {

        //TODO add validation around options

        if (Arr::has($options, 'totalPrice')) {
            $options['adverts'] = ['retailAdverts' => [
                'price' => [
                    'amountGBP' => 14817],
            ],
            ];
            unset($options['totalPrice']);
        }

        return $this->performRequest(
            HttpMethods::POST,
            AutoTraderEndpoints::VehicleMetrics->value . '?advertiserId=' . $advertiserId,
            [],
            array_merge(['vehicle' => ['derivativeId' => $derivativeId,
                'odometerReadingMiles' => $odometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'), ]], $options),
        );

    }
}
