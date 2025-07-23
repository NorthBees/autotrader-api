<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderDeliveryTrait
{
    /**
     * Get delivery details by delivery ID
     *
     * @param int $advertiserId
     * @param string $deliveryId
     * @return array
     */
    public function getDelivery(int $advertiserId, string $deliveryId): array
    {
        $url = AutotraderEndpoints::Delivery->value . '/' . $deliveryId . '?advertiserId=' . $advertiserId;

        return $this->performRequest(HttpMethods::GET, $url, [], []);
    }
}