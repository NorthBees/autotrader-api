<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\Vehicles\Models\Vehicle;

trait AutotraderCoDriverTrait
{
    public function generateDescription(int $advertiserId, Vehicle $vehicle)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::CoDriver->value.'/'.$vehicle->stock_id.'?description=true&advertiserId='.$advertiserId,
            [],
            [
                'advertiserId' => $advertiserId,
                'derivativeId' => $vehicle->derivative_id,
                'firstRegistrationDate' => $vehicle->first_registered_at?->format('Y-m-d'),
                'batteryRangeMiles' => $vehicle->technicalData->battery_range_miles,
                'owners' => $vehicle->technicalData->owners,
                'odometerReadingMiles' => $vehicle->mileage,
            ],
        );
    }

    public function imageOrder(int $advertiserId, Vehicle $vehicle)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::CoDriver->value.'/'.$vehicle->stock_id.'?images=true&advertiserId='.$advertiserId,
            [],
            [
                'advertiserId' => $advertiserId,
            ],
        );
    }
}
