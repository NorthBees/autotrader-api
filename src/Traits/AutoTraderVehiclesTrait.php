<?php

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutotraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutoTraderMissingOdometerException;

trait AutoTraderVehiclesTrait
{
    public function getVehicle(int $advertiserId, string $vrm, ?int $odometerReadingMiles = null, array $options = [
        'features' => true,
        'motTests' => false,
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
