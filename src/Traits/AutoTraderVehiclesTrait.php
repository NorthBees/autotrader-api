<?php

namespace NorthBees\AutoTraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderMissingOdometerException;

trait AutoTraderVehiclesTrait
{
    public function getVehicle(int $advertiserId, string $vrm, ?int $odometer_value = null, array $options = [
        'features' => true,
        'motTests' => false,
        'basicVehicleCheck' => false,
        'fullVehicleCheck' => false,
        'valuations' => false,
        'vehicleMetrics' => false,
    ])
    {

        throw_if((! $odometer_value && (Arr::get($options, 'valuations') || Arr::get($options, 'metrics'))), AutoTraderMissingOdometerException::class);

        $url = implode('/', [$this->getEndpoint(), AutoTraderEndpoints::Vehicles->value]);

        return $this->performRequest(HttpMethods::GET, $url,
            [],
            array_merge([
                'registration' => $vrm,
                'advertiserId' => $advertiserId,
            ], $options));

    }
}
