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
use NorthBees\AutotraderApi\Validators\AutotraderCompetitorStockValidator;

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
        'competitors' => 'false',
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

    /**
     * Search for competitor stock using the searchType=competitor parameter.
     *
     * AutoTrader's competitor search is limited to a maximum page size of 20 and
     * allows pagination up to 10 pages, giving a total of up to 200 vehicles.
     *
     * The competitor href URL returned by getStockList() or getVehicle() with
     * competitors=true (via links.competitors.href) can also be executed directly
     * using getCompetitorStockFromUrl().
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  array  $filters  Competitor filter parameters (standardMake, standardModel, minPlate, etc.)
     * @param  array  $options  Additional options (valuations, page, pageSize, etc.)
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getCompetitorStock(int $advertiserId, array $filters = [], array $options = []): array
    {
        $validator = new AutotraderCompetitorStockValidator;
        $validated = $validator->validate(array_merge($filters, $options));

        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Stock->value,
            [],
            array_merge($validated, [
                'searchType' => 'competitor',
                'advertiserId' => $advertiserId,
            ]),
        );
    }

    /**
     * Execute a pre-built competitor stock search URL returned by the AutoTrader API.
     *
     * When calling getStockList() or getVehicle() with competitors=true, the response
     * includes a links.competitors.href URL. Pass that URL directly to this method to
     * retrieve the competitor vehicles without having to re-assemble the parameters.
     *
     * Example href:
     * https://api.autotrader.co.uk/stock?searchType=competitor&valuations=true&advertiserId=12345&...
     *
     * @param  string  $competitorHref  The full competitor href URL from links.competitors.href
     * @return array
     *
     * @throws AutotraderException
     */
    public function getCompetitorStockFromUrl(string $competitorHref): array
    {
        $parsed = parse_url($competitorHref);
        parse_str($parsed['query'] ?? '', $queryParams);

        throw_if(
            empty($queryParams['advertiserId']),
            AutotraderException::class,
            'The competitor href URL must contain an advertiserId parameter.',
        );

        $path = ltrim($parsed['path'] ?? AutotraderEndpoints::Stock->value, '/');

        return $this->performRequest(
            HttpMethods::GET,
            $path,
            [],
            $queryParams,
        );
    }
}
