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
     * Field removals (as of Mar 2026):
     * - financeTerms.product has been removed - use financeTerms.productType instead
     * - affordability.replacingExistingLoan has been removed - use applicant.replacingExistingLoan instead
     * - affordability.affordableLoan has been removed
     *
     * Field removals (as of Oct 2025):
     * - applicant.surname removed - use applicant.lastName instead
     * - applicant.monthlyRentOrMortgageGBP.amountGBP removed - use applicant.monthlyRentOrMortgage.amountGBP instead
     * - applicant.monthlyChildCareGBP.amountGBP removed - use applicant.monthlyChildcare.amountGBP instead
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  array  $financeData  The finance application data
     * @return array
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
     * Response field removals (as of Mar 2026):
     * - product has been removed - use productType instead
     * - productName has been added to include the lender specific name for the product
     *
     * Response field removals (as of Oct 2025):
     * - questions removed - use quotesRequirements instead
     * - ineligibilityReasons removed - use quotesRequirements instead
     *
     * Response includes (as of Oct 2025):
     * - proposalRequirements: Details of what an applicant needs to provide to create a finance proposal
     * - quotesRequirements: Details of what additional information may be required to produce finance quotes
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  array  $vehicleData  The vehicle data for finance options lookup
     * @return array
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
     * Field removals (as of Mar 2026):
     * - product has been removed in proposals - use productType instead
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $applicationId  The finance application ID
     * @param  array  $financeData  The finance data to update
     * @return array
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

    /**
     * Get a finance application by ID
     *
     * As of Mar 2026, anonymised finance applications return HTTP 200
     * with a payload of {applicationId, status: "Expired"} instead of the
     * previous HTTP 451 error. Check the 'status' field in the response
     * to determine if an application has been anonymised.
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $applicationId  The finance application ID
     * @return array
     */
    public function getFinanceApplication(int $advertiserId, string $applicationId)
    {
        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Finance->value.'/'.$applicationId,
            [],
            ['advertiserId' => $advertiserId]
        );
    }
}
