<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Arr;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderMissingOdometerException;

trait AutotraderVehiclesTrait
{
    /**
     * Options whose data Autotrader can only derive from an odometer reading.
     */
    private const ODOMETER_DEPENDENT_OPTIONS = ['valuations', 'vehicleMetrics'];

    /**
     * Look up a vehicle by registration.
     *
     * The response is returned exactly as the API sends it — a `results` array of records,
     * alongside `totalResults` and any service level `warnings`:
     *
     *     ['results' => [['vehicle' => [...], 'warnings' => [...]]], 'totalResults' => 1]
     *
     * Record level data lives under `results.0`, and record level warnings under
     * `results.0.warnings`. See UPGRADING.md for migrating from the flattened 1.x shape.
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
        throw_if(
            ! $odometerReadingMiles && $this->requiresOdometerReading($options),
            AutotraderMissingOdometerException::class,
        );

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

    /**
     * Whether any requested option needs an odometer reading.
     *
     * Option values are the strings 'true' and 'false', so they are parsed rather than
     * evaluated for truthiness — 'false' is a non-empty string and would otherwise pass.
     *
     * @param  array<string, mixed>  $options
     */
    protected function requiresOdometerReading(array $options): bool
    {
        foreach (self::ODOMETER_DEPENDENT_OPTIONS as $option) {
            if (filter_var(Arr::get($options, $option, false), FILTER_VALIDATE_BOOLEAN)) {
                return true;
            }
        }

        return false;
    }
}
