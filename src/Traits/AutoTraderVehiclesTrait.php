<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderMissingOdometerException;

trait AutoTraderVehiclesTrait
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

        throw_if((! $odometerReadingMiles && (Arr::get($options, 'valuations') || Arr::get($options, 'metrics'))), AutoTraderMissingOdometerException::class);

        return $this->performRequest(
            HttpMethods::GET,
            AutoTraderEndpoints::Vehicles->value,
            [],
            array_merge([
                'registration' => $vrm,
                'odometerReadingMiles' => $odometerReadingMiles,
                'advertiserId' => $advertiserId,
            ], $options),
        );

    }
}
