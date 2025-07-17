<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderValuationsTrait
{
    public function getValuation(int $advertiserId, string $derivativeId, int $odometerReadingMiles, Carbon $firstRegistrationDate, array $options = [
        'totalPrice' => null,
        'features' => null,
        'conditionRating' => null,
        'priceIndicatorRatingBands' => "false",
    ])
    {

        if (Arr::has($options, 'totalPrice')) {
            $options['adverts'] = ['retailAdverts' => [
                'price' => [
                    'amountGBP' => Arr::get($options, 'totalPrice')],
            ],
            ];
            unset($options['totalPrice']);
        }

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Valuations->value . '?advertiserId=' . $advertiserId,
            [],
            array_merge(['vehicle' => ['derivativeId' => $derivativeId,
                'odometerReadingMiles' => $odometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'), ]], $options),
        );

    }
}
