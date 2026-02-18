<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderCoDriverTrait
{
    /**
     * Generate a description for a vehicle using CoDriver.
     *
     * @param  object  $vehicle  An object with properties: stock_id, derivative_id, first_registered_at, technicalData (battery_range_miles, owners), mileage
     */
    public function generateDescription(int $advertiserId, object $vehicle)
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

    /**
     * Get the recommended image order for a vehicle using CoDriver.
     *
     * @param  object  $vehicle  An object with property: stock_id
     */
    public function imageOrder(int $advertiserId, object $vehicle)
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
