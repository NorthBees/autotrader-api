<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;

trait AutoTraderHistoricValuationsTrait
{
    public function getHistoricValuation(int $advertiserId, string $derivativeId, int $historicOdometerReadingMiles, Carbon $firstRegistrationDate, Carbon $historicValuationDate)
    {

        if ($historicValuationDate->isBefore($firstRegistrationDate)) {
            throw new AutoTraderException('Historic valuation cannot be from before the vehicle was registered');
        }

        if ($historicValuationDate->isAfter(now())) {
            throw new AutoTraderException('Historic valuation cannot be a future date');
        }

        return $this->performRequest(
            HttpMethods::POST,
            AutoTraderEndpoints::HistoricValuations->value . '?advertiserId=' . $advertiserId,
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
