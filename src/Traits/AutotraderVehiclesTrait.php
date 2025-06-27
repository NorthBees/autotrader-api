<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderMissingOdometerException;

trait AutotraderVehiclesTrait
{
    public function getVehicle(int $advertiserId, string $vrm, ?int $odometerReadingMiles = null, array $options = [
        'chargeTimes' => "false",
        'competitors' => "false",
        'features' => "false",
        'motTests' => "false",
        'history' => "false",
        'fullVehicleCheck' => "false",
        'valuations' => "false",
        'vehicleMetrics' => "false",
    ])
    {

        throw_if((! $odometerReadingMiles && (Arr::get($options, 'valuations') || Arr::get($options, 'metrics'))), AutotraderMissingOdometerException::class);

        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Vehicles->value,
            [],
            array_merge([
                'registration' => $vrm,
                'odometerReadingMiles' => $odometerReadingMiles,
                'advertiserId' => $advertiserId,
            ], $options),
        );

    }
}
