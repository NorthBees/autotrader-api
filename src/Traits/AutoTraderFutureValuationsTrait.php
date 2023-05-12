<?php

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutotraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutoTraderException;

trait AutoTraderFutureValuationsTrait
{
    public function getFutureValuation(int $advertiserId, string $derivativeId, int $futureOdometerReadingMiles, Carbon $firstRegistrationDate, Carbon $futureValuationDate)
    {

        if (now()->isAfter($futureValuationDate)) {
            throw new AutoTraderException('Future valuation date must be in the future!');
        }

        return $this->performRequest(HttpMethods::POST, AutoTraderEndpoints::FutureValuations->value.'?advertiserId='.$advertiserId,
            [],
            ['vehicle' => [
                'derivativeId' => $derivativeId,
                'futureOdometerReadingMiles' => $futureOdometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'),
            ],
                'futureValuationDate' => $futureValuationDate->format('Y-m-d'),
            ]);

    }
}
