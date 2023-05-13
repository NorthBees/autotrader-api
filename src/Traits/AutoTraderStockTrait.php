<?php

namespace NorthBees\AutoTraderApi\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderMissingOdometerException;

trait AutoTraderStockTrait
{
    public function getStockList(int $advertiserId, array $options = [
        'competitors' => false,
        'features' => true,
        'responseMetrics' => false,
        'vehicleMetrics' => false,
        'valuations' => false,
    ])
    {

        return $this->performRequest(HttpMethods::GET, AutoTraderEndpoints::Vehicles->value,
            [],
            array_merge([
            ], $options));

    }

    public function createStock(int $advertiserId, array $vehicleData)
    {
        return $this->performRequest(HttpMethods::POST, AutoTraderEndpoints::Stock->value . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData);
    }

    public function updateStock(int $advertiserId, array $vehicleData)
    {
        $validator = Validator::make($vehicleData, [
            'metadata.stockId' => 'required',
        ]);

        if ($validator->fails()) {
            throw new AutoTraderException('metadata=>stockId is required');
        }

        return $this->performRequest(HttpMethods::PATCH, AutoTraderEndpoints::Stock->value . '?advertiserId=' . $advertiserId,
            [],
            $vehicleData);

    }

    public function getStockFeatures(int $advertiserId, string $stockId)
    {
        $url = implode('/', [AutoTraderEndpoints::Stock->value, $stockId, 'features']);
        return $this->performRequest(HttpMethods::GET, $url . '?advertiserId=' . $advertiserId, [], []);
    }
}
