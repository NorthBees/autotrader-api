<?php

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
    public function getStockList(int $advertiserId, array $filters = [], array $options = [
        'competitors' => false,
        'features' => true,
        'responseMetrics' => false,
        'vehicleMetrics' => false,
        'valuations' => false,
        'media' => true,
        'chargeTimes' => true,
        'motHistory' => true,
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
            throw new AutoTraderException($validator->errors());
        }

        return $this->performRequest(
            HttpMethods::GET,
            AutoTraderEndpoints::Stock->value,
            [],
            array_merge($filters, $options, ['advertiserId'=>$advertiserId]),
        );

    }

    public function createStock(int $advertiserId, array $vehicleData)
    {
        return $this->performRequest(
            HttpMethods::POST,
            AutoTraderEndpoints::Stock->value . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData,
        );
    }

    public function updateStock(int $advertiserId, array $vehicleData)
    {

        throw_if(! Arr::has($vehicleData, 'metadata.stockId'), AutoTraderException::class, ('metadata=>stockId is required'));

        return $this->performRequest(
            HttpMethods::PATCH,
            AutoTraderEndpoints::Stock->value . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData,
        );

    }

    public function getStockFeatures(int $advertiserId, string $stockId)
    {
        $url = implode('/', [AutoTraderEndpoints::Stock->value, $stockId, 'features']);

        return $this->performRequest(HttpMethods::GET, $url . '?advertiserId=' . $advertiserId, [], []);
    }
}
