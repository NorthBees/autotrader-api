<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\AutoTraderLifecycleStates;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;

trait AutoTraderStockTrait
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
    ])
    {

        $validator = Validator::make($filters, [
            'lifecycleState' => ['nullable', new Enum(AutoTraderLifecycleStates::class)],
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:200',
            'registration' => 'nullable|string',
            'searchId' => 'nullable|integer',
            'stockId' => 'nullable|string',
            'vin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new AutoTraderException((string) $validator->errors());
        }

        return $this->performRequest(
            HttpMethods::GET,
            AutoTraderEndpoints::Stock->value,
            [],
            array_merge($filters, $options, ['advertiserId' => $advertiserId]),
        );

    }

    public function createStock(string $advertiserId, array $vehicleData)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutoTraderEndpoints::Stock->value . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData,
        );
    }

    public function updateStock(string $advertiserId, array $vehicleData)
    {

        throw_if(! Arr::has($vehicleData, 'metadata.stockId'), AutoTraderException::class, ('metadata=>stockId is required'));

        return $this->performRequest(
            HttpMethods::PATCH,
            AutoTraderEndpoints::Stock->value . '/' . $vehicleData['metadata']['stockId'] . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData,
        );

    }

    public function getStockFeatures(string $advertiserId, string $stockId)
    {
        $url = implode('/', [AutoTraderEndpoints::Stock->value, $stockId, 'features']);

        return $this->performRequest(HttpMethods::GET, $url . '?advertiserId=' . $advertiserId, [], []);
    }
}
