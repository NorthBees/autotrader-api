<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\AutotraderTaxonomies;
use NorthBees\AutotraderApi\Enum\AutotraderTaxonomyFacets;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Enum\VehicleTypes;

trait AutotraderTaxonomyTrait
{
    public function getVehicleTypes(int $advertiserId)
    {
        return $this->getTaxonomy($advertiserId, AutotraderTaxonomies::VEHICLETYPES);
    }

    public function getTaxonomy(
        int $advertiserId,
        AutotraderTaxonomies|AutotraderTaxonomyFacets $taxonomy,
        array $options = [],
    ) {
        $url = implode('/', [AutotraderEndpoints::Taxonomy->value, $taxonomy->value]);

        return $this->performRequest(
            HttpMethods::GET,
            $url,
            [],
            array_merge([
                'advertiserId' => $advertiserId,
            ], $options),
        );

    }

    public function getMakes(int $advertiserId, VehicleTypes $vehicleType, ?string $productionStatus = null)
    {
        return $this->getTaxonomy(
            $advertiserId,
            AutotraderTaxonomies::MAKES,
            [
                'vehicleType' => $vehicleType->value,
                'productionStatus' => $productionStatus,
            ],
        );
    }

    public function getModels(int $advertiserId, string $makeId, ?string $model = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutotraderTaxonomies::MODELS, [
            'makeId' => $makeId,
            'model' => $model,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getGenerations(int $advertiserId, ?string $modelId = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutotraderTaxonomies::GENERATIONS, [
            'modelId' => $modelId,
            'productionStatus' => $productionStatus,
        ]);
    }

    /**
     * Get derivatives for a generation.
     *
     * @param int $advertiserId The advertiser ID
     * @param string $generationId The generation ID
     * @param string|null $productionStatus Optional production status filter (Current, Discontinued, Future)
     * @param string|null $oemModelCode Optional OEM model code to search for a specific derivative (e.g. Volvo derivatives)
     * @return array
     */
    public function getDerivatives(int $advertiserId, string $generationId, ?string $productionStatus = null, ?string $oemModelCode = null)
    {
        $options = [
            'generationId' => $generationId,
            'productionStatus' => $productionStatus,
        ];

        if ($oemModelCode !== null) {
            $options['oemModelCode'] = $oemModelCode;
        }

        return $this->getTaxonomy($advertiserId, AutotraderTaxonomies::DERIVATIVES, $options);
    }

    public function getFeatures(int $advertiserId, string $derivativeId, Carbon $effectiveDate, ?string $productionStatus = null, array $options = [
        'factoryCodes' => "false",
    ])
    {
        return $this->getTaxonomy($advertiserId, AutotraderTaxonomies::FEATURES, array_merge([
            'derivativeId' => $derivativeId,
            'effectiveDate' => $effectiveDate->format('Y-m-d'),
            'productionStatus' => $productionStatus,
        ], $options));
    }

    public function getPrices(int $advertiserId, string $derivativeId, ?Carbon $effectiveDate = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutotraderTaxonomies::PRICES, [
            'derivativeId' => $derivativeId,
            'effectiveDate' => $effectiveDate ? $effectiveDate->format('Y-m-d') : null,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getTechnicalData(int $advertiserId, string $derivativeId)
    {

        $url = implode('/', [AutotraderEndpoints::Taxonomy->value, AutotraderTaxonomies::DERIVATIVES->value, $derivativeId]);
        $options = [];

        return $this->performRequest(
            HttpMethods::GET,
            $url,
            [],
            array_merge([
                'advertiserId' => $advertiserId,
            ], $options),
        );

    }

    public function getFacets(int $advertiserId, AutotraderTaxonomyFacets $facet, string $generationId, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, $facet, [
            'generationId' => $generationId,
            'productionStatus' => $productionStatus,
        ]);
    }
}
