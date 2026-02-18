<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderHistoricValuationsTrait
{
    public function getHistoricValuation(int $advertiserId, string $derivativeId, int $historicOdometerReadingMiles, Carbon $firstRegistrationDate, Carbon $historicValuationDate)
    {

        if ($historicValuationDate->isBefore($firstRegistrationDate)) {
            throw new AutotraderException('Historic valuation cannot be from before the vehicle was registered');
        }

        if ($historicValuationDate->isAfter(now())) {
            throw new AutotraderException('Historic valuation cannot be a future date');
        }

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::HistoricValuations->value.'?advertiserId='.$advertiserId,
            [],
            ['vehicle' => [
                'derivativeId' => $derivativeId,
                'historicOdometerReadingMiles' => $historicOdometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'),
            ],
                'historicValuationDate' => $historicValuationDate->format('Y-m-d'),
            ],
        );

    }
}
