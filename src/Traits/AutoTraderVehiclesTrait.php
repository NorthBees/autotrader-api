<?php

namespace NorthBees\AutoTraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderMissingOdometerException;

trait AutoTraderVehiclesTrait
{
    public function getVehicle(int $advertiserId, string $vrm, ?int $odometerReadingMiles = null, array $options = [
        'chargeTimes' => false,
        'competitors' => false,
        'features' => true,
        'motTests' => false,
        'history' => false,
        'basicVehicleCheck' => false,
        'fullVehicleCheck' => false,
        'valuations' => false,
        'vehicleMetrics' => false,
    ])
    {

        throw_if((! $odometerReadingMiles && (Arr::get($options, 'valuations') || Arr::get($options, 'metrics'))), AutoTraderMissingOdometerException::class);

        return $this->performRequest(HttpMethods::GET, AutoTraderEndpoints::Vehicles->value,
            [],
            array_merge([
                'registration' => $vrm,
                'advertiserId' => $advertiserId,
            ], $options));

    }
}
