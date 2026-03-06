<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderHistoricValuationsTrait
{
    /**
     * Get a historic valuation for a vehicle
     *
     * Response includes (as of Mar 2026):
     * - amountNoVatGBP valuations for retail, trade, and partExchange markets
     *   Only available for LCVs (Light Commercial Vehicles), produced alongside amountExVatGBP
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $derivativeId  The vehicle derivative ID
     * @param  int  $historicOdometerReadingMiles  The historic odometer reading in miles
     * @param  Carbon  $firstRegistrationDate  The vehicle's first registration date
     * @param  Carbon  $historicValuationDate  The date to value the vehicle at (must be in the past)
     * @return array
     *
     * @throws AutotraderException
     */
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
