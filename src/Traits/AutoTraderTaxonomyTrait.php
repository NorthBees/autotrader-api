<?php

namespace NorthBees\AutoTraderApi\Traits;

use Carbon\Carbon;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\AutoTraderTaxonomies;
use NorthBees\AutoTraderApi\Enum\AutoTraderTaxonomyFacets;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Enum\VehicleTypes;

trait AutoTraderTaxonomyTrait
{
    public function getVehicleTypes(int $advertiserId)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::VehicleTypes);
    }

    public function getTaxonomy(
        int $advertiserId,
        AutoTraderTaxonomies|AutoTraderTaxonomyFacets $taxonomy,
        array $options = [],
    ) {
        $url = implode('/', [AutoTraderEndpoints::Taxonomy->value, $taxonomy->value]);

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
            AutoTraderTaxonomies::Makes,
            [
                'vehicleType' => $vehicleType->value,
                'productionStatus' => $productionStatus,
            ],
        );
    }

    public function getModels(int $advertiserId, string $makeId, ?string $model = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Models, [
            'makeId' => $makeId,
            'model' => $model,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getGenerations(int $advertiserId, ?string $modelId = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Generations, [
            'modelId' => $modelId,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getDerivatives(int $advertiserId, string $generationId, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Derivatives, [
            'generationId' => $generationId,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getFeatures(int $advertiserId, string $derivativeId, Carbon $effectiveDate, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Features, [
            'derivativeId' => $derivativeId,
            'effectiveDate' => $effectiveDate->format('Y-m-d'),
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getPrices(int $advertiserId, string $derivativeId, ?Carbon $effectiveDate = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Prices, [
            'derivativeId' => $derivativeId,
            'effectiveDate' => $effectiveDate ? $effectiveDate->format('Y-m-d') : null,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getTechnicalData(int $advertiserId, string $derivativeId)
    {

        $url = implode('/', [AutoTraderEndpoints::Taxonomy->value, AutoTraderTaxonomies::Derivatives->value, $derivativeId]);
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

    public function getFacets(int $advertiserId, AutoTraderTaxonomyFacets $facet, string $generationId, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, $facet, [
            'generationId' => $generationId,
            'productionStatus' => $productionStatus,
        ]);
    }
}
