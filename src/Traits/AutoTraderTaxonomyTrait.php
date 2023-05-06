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
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::VehicleTypes->value);
    }

    public function getTaxonomy(
        int $advertiserId,
        AutoTraderTaxonomies|AutoTraderTaxonomyFacets $taxonomy,
        array $options = []
    ) {
        $url = implode('/', [$this->getEndpoint(), AutoTraderEndpoints::Taxonomy->value, $taxonomy->value]);

        return $this->performRequest(HttpMethods::GET, $url,
            [],
            array_merge([
                'advertiserId' => $advertiserId,
            ], $options));

    }

    public function getMakes(int $advertiserId, VehicleTypes $vehicleType, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Makes,
            [
                'vehicleType' => $vehicleType,
                'productionStatus' => $productionStatus,
            ]);
    }

    public function getModels(int $advertiserId, string $makeId, ?string $model = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Models, [
            'makeId' => $makeId,
            'model' => $model,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getGenerations(int $advertiserId, string $modelId = null, ?string $productionStatus = null)
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

    public function getFeatures(int $advertiserId, string $generationId, Carbon $effectiveDate, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Features, [
            'generationId' => $generationId,
            'effectiveDate' => $effectiveDate->format('Y-m-d'),
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getPrices(int $advertiserId, string $generationId, ?Carbon $effectiveDate = null, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Prices, [
            'generationId' => $generationId,
            'effectiveDate' => $effectiveDate ? $effectiveDate->format('Y-m-d') : null,
            'productionStatus' => $productionStatus,
        ]);
    }

    public function getTechnicalData(int $advertiserId, string $derivativeId)
    {
        return $this->getTaxonomy($advertiserId, AutoTraderTaxonomies::Derivatives, [
            'derivativeId' => $derivativeId,
        ]);
    }

    public function getFacets(int $advertiserId, AutoTraderTaxonomyFacets $facet, string $generationId, ?string $productionStatus = null)
    {
        return $this->getTaxonomy($advertiserId, $facet, [
            'generationId' => $generationId,
            'productionStatus' => $productionStatus,
        ]);
    }
}
