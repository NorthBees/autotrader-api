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
     *
     * Field deprecations (as of Feb 2026):
     * - financeTerms.product is @deprecated - use financeTerms.productType instead
     * - affordability.replacingExistingLoan is @deprecated - use applicant.replacingExistingLoan instead
     *
     * Field deprecations (as of Oct 2025):
     * - applicant.surname is @deprecated and removed - use applicant.lastName instead
     * - applicant.monthlyRentOrMortgageGBP.amountGBP is @deprecated and removed - use applicant.monthlyRentOrMortgage.amountGBP instead
     * - applicant.monthlyChildCareGBP.amountGBP is @deprecated and removed - use applicant.monthlyChildcare.amountGBP instead
     */
    public function submitFinanceApplication(int $advertiserId, array $financeData)
    {
        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Finance->value,
            [],
            array_merge(['advertiserId' => $advertiserId], $financeData)
        );
    }

    /**
     * Get finance options for a vehicle (Quotes endpoint)
     *
     * Response field deprecations (as of Feb 2026):
     * - product is @deprecated - use productType instead
     * - productName has been added to include the lender specific name for the product
     *
     * Response field deprecations (as of Oct 2025):
     * - questions is @deprecated and removed - use quotesRequirements instead
     * - ineligibilityReasons is @deprecated and removed - use quotesRequirements instead
     *
     * Response includes (as of Oct 2025):
     * - proposalRequirements: Details of what an applicant needs to provide to create a finance proposal
     * - quotesRequirements: Details of what additional information may be required to produce finance quotes
     */
    public function getFinanceOptions(int $advertiserId, array $vehicleData)
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
     *
     * Field deprecations (as of Feb 2026):
     * - product is @deprecated in proposals - use productType instead
     */
    public function updateFinanceApplication(int $advertiserId, string $applicationId, array $financeData)
    {
        return $this->performRequest(
            HttpMethods::PATCH,
            AutotraderEndpoints::Finance->value.'/'.$applicationId,
            [],
            array_merge(['advertiserId' => $advertiserId], $financeData)
        );
    }
}
