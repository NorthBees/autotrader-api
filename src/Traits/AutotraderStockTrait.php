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
    /**
     * Get a list of stock items for an advertiser
     *
     * Response includes (as of Aug 2025):
     * - eligibleContractAllowances: Stock allowances the item is eligible for
     * - allocatedContractAllowance: The allowance allocated for the published stock item
     * - rarityRating, valueRating: Autotrader intelligence ratings for vehicle features
     *
     * Response includes (as of Oct 2025):
     * - vehicle.origin: Indicates if the vehicle is UK or Non UK specification
     *
     * When updating stock lifecycle state to SOLD with a published tradeAdvert,
     * you can now set tradeAdvert to NOT_PUBLISHED to unpublish the record (as of Feb 2026).
     */
    public function getStockList(int $advertiserId, array $filters = [], array $options = [
        'vehicle' => 'true',
        'advertiser' => 'true',
        'adverts' => 'true',
        'finance' => 'false',
        'metadata' => 'true',
        'features' => 'false',
        'media' => 'false',
        'responseMetrics' => 'false',
        'check' => 'false',
        'motTests' => 'false',
        'factoryCodes' => 'false',
        'priceIndicatorRatingBands' => 'false',
        'wheelbaseMM' => 'false',
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

    public function createStock(int $advertiserId, array $vehicleData)
    {

        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Stock->value.'?advertiserId='.$advertiserId,
            [],
            $vehicleData,
        );
    }

    /**
     * Update a stock item
     *
     * When updating lifecycle state to SOLD and the vehicle has a published tradeAdvert,
     * you can include tradeAdvert status as NOT_PUBLISHED to unpublish the record.
     * Use AutotraderTradeAdvertStates::NOT_PUBLISHED for the tradeAdvert.status value.
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  array  $vehicleData  The vehicle data including metadata.stockId
     * @return array
     *
     * @throws AutotraderException
     */
    public function updateStock(int $advertiserId, array $vehicleData)
    {

        throw_if(! Arr::has($vehicleData, 'metadata.stockId'), AutotraderException::class, ('metadata=>stockId is required'));

        return $this->performRequest(
            HttpMethods::PATCH,
            AutotraderEndpoints::Stock->value.'/'.$vehicleData['metadata']['stockId'].'?advertiserId='.$advertiserId,
            [],
            $vehicleData,
        );

    }

    public function getStockFeatures(int $advertiserId, string $stockId)
    {
        $url = implode('/', [AutotraderEndpoints::Stock->value, $stockId, 'features']);

        return $this->performRequest(HttpMethods::GET, $url.'?advertiserId='.$advertiserId, [], []);
    }

    /**
     * Get a real-time summary of state related information for a given stock ID
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $stockId  The stock ID
     * @return array
     */
    public function getStockSummary(int $advertiserId, string $stockId)
    {
        $url = implode('/', [AutotraderEndpoints::Stock->value, $stockId, 'summary']);

        return $this->performRequest(HttpMethods::GET, $url.'?advertiserId='.$advertiserId, [], []);
    }
}
