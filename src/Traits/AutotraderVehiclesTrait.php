<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderMissingOdometerException;
use NorthBees\AutotraderApi\Support\VehicleResponseNormaliser;

trait AutotraderVehiclesTrait
{
    /**
     * Look up a vehicle by registration.
     *
     * As of August 2026 the API wraps the payload in a `results` array (the historic
     * `vehicle` root is served alongside it until 28 October 2026), and splits warnings
     * between service level and record level. The response is normalised back to the flat
     * shape via {@see VehicleResponseNormaliser}, with `serviceWarnings` and
     * `recordWarnings` added for callers that need the distinction.
     */
    public function getVehicle(int $advertiserId, string $vrm, ?int $odometerReadingMiles = null, array $options = [
        'chargeTimes' => 'false',
        'competitors' => 'false',
        'features' => 'false',
        'motTests' => 'false',
        'history' => 'false',
        'fullVehicleCheck' => 'false',
        'valuations' => 'false',
        'vehicleMetrics' => 'false',
        'factoryCodes' => 'false',
    ])
    {

        throw_if((! $odometerReadingMiles && (Arr::get($options, 'valuations') || Arr::get($options, 'metrics'))), AutotraderMissingOdometerException::class);

        return VehicleResponseNormaliser::normalise(
            $this->performRequest(
                HttpMethods::GET,
                AutotraderEndpoints::Vehicles->value,
                [],
                array_merge([
                    'registration' => $vrm,
                    'odometerReadingMiles' => $odometerReadingMiles,
                    'advertiserId' => $advertiserId,
                ], $options),
            ),
        );

    }
}
