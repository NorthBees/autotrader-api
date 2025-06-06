<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\Vehicles\Models\Vehicle;

trait AutoTraderCoDriverTrait
{
    public function generateDescription(string $advertiserId, Vehicle $vehicle)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutoTraderEndpoints::CoDriver->value . '/' . $vehicle->stock_id . '?description=true&advertiserId=' . $advertiserId,
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

    public function imageOrder(string $advertiserId, Vehicle $vehicle)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutoTraderEndpoints::CoDriver->value . '/' . $vehicle->stock_id . '?images=true&advertiserId=' . $advertiserId,
            [],
            [
                'advertiserId' => $advertiserId,
            ],
        );
    }
}
