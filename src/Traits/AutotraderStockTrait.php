<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\AutotraderLifecycleStates;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderStockTrait
{
    public function getStockList(string $advertiserId, array $filters = [], array $options = [
        'vehicle' => "true",
        'advertiser' => "true",
        'adverts' => "true",
        'finance' => "false",
        'metadata' => "true",
        'features' => "false",
        'media' => "false",
        'responseMetrics' => "false",
        'check' => "false",
        'motTests' => "false",
        'factoryCodes' => "false",
        'priceIndicatorRatingBands' => "false",
        'wheelbaseMM' => "false",
    ])
    {

        $validator = Validator::make($filters, [
            'lifecycleState' => ['nullable', new Enum(AutotraderLifecycleStates::class)],
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:200',
            'registration' => 'nullable|string',
            'searchId' => 'nullable|integer',
            'stockId' => 'nullable|string',
            'vin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new AutotraderException((string) $validator->errors());
        }

        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Stock->value,
            [],
            array_merge($filters, $options, ['advertiserId' => $advertiserId]),
        );

    }

    public function createStock(string $advertiserId, array $vehicleData)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Stock->value . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData,
        );
    }

    public function updateStock(string $advertiserId, array $vehicleData)
    {

        throw_if(! Arr::has($vehicleData, 'metadata.stockId'), AutotraderException::class, ('metadata=>stockId is required'));

        return $this->performRequest(
            HttpMethods::PATCH,
            AutotraderEndpoints::Stock->value . '/' . $vehicleData['metadata']['stockId'] . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData,
        );

    }

    public function getStockFeatures(string $advertiserId, string $stockId)
    {
        $url = implode('/', [AutotraderEndpoints::Stock->value, $stockId, 'features']);

        return $this->performRequest(HttpMethods::GET, $url . '?advertiserId=' . $advertiserId, [], []);
    }
}
