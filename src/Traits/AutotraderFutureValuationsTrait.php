<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderFutureValuationsTrait
{
    /**
     * Get a future valuation for a vehicle
     *
     * Response includes (as of Mar 2026):
     * - amountNoVatGBP valuations for retail, trade, and partExchange markets
     *   Only available for LCVs (Light Commercial Vehicles), produced alongside amountExVatGBP
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $derivativeId  The vehicle derivative ID
     * @param  int  $futureOdometerReadingMiles  The expected future odometer reading in miles
     * @param  Carbon  $firstRegistrationDate  The vehicle's first registration date
     * @param  Carbon  $futureValuationDate  The future date to value the vehicle at (must be in the future)
     * @return array
     *
     * @throws AutotraderException
     */
    public function getFutureValuation(int $advertiserId, string $derivativeId, int $futureOdometerReadingMiles, Carbon $firstRegistrationDate, Carbon $futureValuationDate)
    {

        if (now()->isAfter($futureValuationDate)) {
            throw new AutotraderException('Future valuation date must be in the future!');
        }

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::FutureValuations->value.'?advertiserId='.$advertiserId,
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
