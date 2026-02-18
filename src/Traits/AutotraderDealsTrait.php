<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Facades\Validator;
use NorthBees\AutotraderApi\Enum\AutotraderDealCancellationReasons;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderDealsTrait
{
    /**
     * Get a list of deals for an advertiser with optional filtering
     *
     * Response includes (as of Nov 2025):
     * - buyingSignals: Consumer behaviour indicators including dealIntentScore, intent, localCustomer, advertSaved, preferences
     *
     * Response field deprecations (as of Jan 2026):
     * - stock.reservationStatus is @deprecated - use reservation object instead
     * - consumerReservationFeeStatus is @deprecated - use reservation object instead
     * The reservation object provides reservation status values including Requested and Reserved.
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  array  $filters  Optional filters (page, from, to)
     * @return array
     *
     * @throws AutotraderException
     */
    public function getDeals(int $advertiserId, array $filters = [])
    {
        $validator = Validator::make($filters, [
            'page' => 'nullable|integer|min:1',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            throw new AutotraderException((string) $validator->errors());
        }

        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Deals->value,
            [],
            array_merge($filters, ['advertiserId' => $advertiserId])
        );
    }

    /**
     * Get a specific deal by ID
     *
     * Response includes (as of Nov 2025):
     * - buyingSignals: Consumer behaviour indicators including dealIntentScore, intent, localCustomer, advertSaved, preferences
     *
     * Response field deprecations (as of Jan 2026):
     * - stock.reservationStatus is @deprecated - use reservation object instead
     * - consumerReservationFeeStatus is @deprecated - use reservation object instead
     * The reservation object provides reservation status values including Requested and Reserved.
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $dealId  The deal ID
     * @return array
     */
    public function getDeal(int $advertiserId, string $dealId)
    {
        return $this->performRequest(
            HttpMethods::GET,
            AutotraderEndpoints::Deals->value.'/'.$dealId,
            [],
            ['advertiserId' => $advertiserId]
        );
    }

    /**
     * Complete a deal by updating its status to Complete
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $dealId  The deal ID
     * @return array
     */
    public function completeDeal(int $advertiserId, string $dealId)
    {
        return $this->updateDeal($advertiserId, $dealId, [
            'advertiserDealStatus' => 'Complete',
        ]);
    }

    /**
     * Cancel a deal with a cancellation reason
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $dealId  The deal ID
     * @param  string  $reason  The cancellation reason (Different Vehicle, Unaffordable, etc.)
     * @param  string|null  $notes  Optional cancellation notes
     * @return array
     *
     * @throws AutotraderException
     */
    public function cancelDeal(int $advertiserId, string $dealId, string $reason, ?string $notes = null)
    {
        $validReasons = AutotraderDealCancellationReasons::values();

        if (! in_array($reason, $validReasons)) {
            throw new AutotraderException('Invalid cancellation reason. Must be one of: '.implode(', ', $validReasons));
        }

        $data = [
            'advertiserDealStatus' => 'Cancelled',
            'advertiserCancellationReason' => $reason,
        ];

        if ($notes !== null) {
            $data['advertiserCancellationNotes'] = $notes;
        }

        return $this->updateDeal($advertiserId, $dealId, $data);
    }

    /**
     * Update a deal with custom data
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $dealId  The deal ID
     * @param  array  $data  The update data
     * @return array
     */
    public function updateDeal(int $advertiserId, string $dealId, array $data)
    {
        return $this->performRequest(
            HttpMethods::PATCH,
            AutotraderEndpoints::Deals->value.'/'.$dealId.'?advertiserId='.$advertiserId,
            [],
            $data
        );
    }

    /**
     * Remove part exchange from a deal
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $dealId  The deal ID
     * @return array
     */
    public function removeDealPartExchange(int $advertiserId, string $dealId)
    {
        return $this->updateDeal($advertiserId, $dealId, [
            'partExchange' => null,
        ]);
    }

    /**
     * Remove finance application from a deal
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  string  $dealId  The deal ID
     * @return array
     */
    public function removeDealFinanceApplication(int $advertiserId, string $dealId)
    {
        return $this->updateDeal($advertiserId, $dealId, [
            'financeApplication' => null,
        ]);
    }

    /**
     * Create a new deal for an advertiser
     *
     * Allows retailers to create Deals which have originated outside of the Autotrader consumer journey.
     * Following creation, the deal can be managed via the Deals API.
     *
     * @param  int  $advertiserId  The advertiser ID
     * @param  array  $dealData  The deal data
     * @return array
     */
    public function createDeal(int $advertiserId, array $dealData)
    {
        return $this->performRequest(
            HttpMethods::POST,
            AutotraderEndpoints::Deals->value.'?advertiserId='.$advertiserId,
            [],
            $dealData
        );
    }
}
