<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Facades\Validator;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderSearchTrait
{
    /**
     * Search for vehicles
     * Supports factoryCodes and wheelbaseMM fields
     */
    public function searchVehicles(string $advertiserId, array $searchCriteria = [], array $options = [
        'vehicle' => "true",
        'advertiser' => "true",
        'adverts' => "true",
        'finance' => "false",
        'metadata' => "true",
        'features' => "false",
        'media' => "false",
        'responseMetrics' => "false",
        'factoryCodes' => "false",
        'wheelbaseMM' => "false",
    ])
    {
        $validator = Validator::make($searchCriteria, [
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'priceFrom' => 'nullable|integer|min:0',
            'priceTo' => 'nullable|integer|min:0',
            'yearFrom' => 'nullable|integer|min:1900',
            'yearTo' => 'nullable|integer|min:1900',
            'mileageFrom' => 'nullable|integer|min:0',
            'mileageTo' => 'nullable|integer|min:0',
            'fuelType' => 'nullable|string',
            'transmissionType' => 'nullable|string',
            'bodyType' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:100',
            'sortBy' => 'nullable|string',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'distance' => 'nullable|integer',
            'postcode' => 'nullable|string',
            'factoryCodes' => 'nullable|array',
            'wheelbaseMM' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new AutotraderException((string) $validator->errors());
        }

        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Search->value,
            [],
            array_merge($searchCriteria, $options, ['advertiserId' => $advertiserId])
        );
    }

    /**
     * Get saved searches
     */
    public function getSavedSearches(string $advertiserId)
    {
        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Search->value . '/saved',
            [],
            ['advertiserId' => $advertiserId]
        );
    }

    /**
     * Save a search
     */
    public function saveSearch(string $advertiserId, array $searchData)
    {
        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Search->value . '/saved',
            ['advertiserId' => $advertiserId],
            $searchData
        );
    }
}