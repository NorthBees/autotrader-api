<?php

namespace NorthBees\AutoTraderApi\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;

trait AutoTraderValuationsTrait
{
    public function getValuation(int $advertiserId, string $derivativeId, int $odometerReadingMiles, Carbon $firstRegistrationDate, array $options = [
        'totalPrice' => null,
        'features' => null,
        'conditionRating' => null,
    ])
    {

        $url = implode('/', [$this->getEndpoint(), AutoTraderEndpoints::Valuations->value]).'?advertiserId='.$advertiserId;

        if (Arr::has($options, 'totalPrice')) {
            $options['adverts'] = ['retailAdverts' => [
                'price' => [
                    'amountGBP' => 14817],
            ],
            ];
            unset($options['totalPrice']);
        }

        return $this->performRequest(HttpMethods::POST, $url,
            [],
            array_merge(['vehicle' => ['derivativeId' => $derivativeId,
                'odometerReadingMiles' => $odometerReadingMiles,
                'firstRegistrationDate' => $firstRegistrationDate->format('Y-m-d'), ]], $options));

    }
}
