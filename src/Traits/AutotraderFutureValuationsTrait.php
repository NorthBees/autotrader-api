<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderFutureValuationsTrait
{
    public function getFutureValuation(int $advertiserId, string $derivativeId, int $futureOdometerReadingMiles, Carbon $firstRegistrationDate, Carbon $futureValuationDate)
    {

        if (now()->isAfter($futureValuationDate)) {
            throw new AutotraderException('Future valuation date must be in the future!');
        }

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::FutureValuations->value . '?advertiserId=' . $advertiserId,
            [],
            ['vehicle' => [
                'derivativeId' => $derivativeId,
                'futureOdometerReadingMiles' => $futureOdometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'),
            ],
                'futureValuationDate' => $futureValuationDate->format('Y-m-d'),
            ],
        );

    }
}
