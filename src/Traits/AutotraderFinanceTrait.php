<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderFinanceTrait
{
    /**
     * Submit finance application data
     * Note: Years fields have been removed - only months fields are used
     */
    public function submitFinanceApplication(string $advertiserId, array $financeData)
    {
        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Finance->value,
            ['advertiserId' => $advertiserId],
            [],
            $financeData
        );
    }

    /**
     * Get finance options for a vehicle
     */
    public function getFinanceOptions(string $advertiserId, array $vehicleData)
    {
        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Finance->value,
            [],
            array_merge(['advertiserId' => $advertiserId], $vehicleData)
        );
    }

    /**
     * Update finance application
     */
    public function updateFinanceApplication(string $advertiserId, string $applicationId, array $financeData)
    {
        return $this->performRequest(
            HttpMethods::PATCH,
            AutotraderEndpoints::Finance->value . '/' . $applicationId . '?advertiserId=' . $advertiserId,
            [],
            $financeData
        );
    }
}