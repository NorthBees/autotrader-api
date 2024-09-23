<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;

trait AutoTraderAdvertisersTrait
{
    public function getAdvertisers(array $options = [])
    {

        return $this->performRequestWithoutAdvertiserId(
            HttpMethods::GET,
            AutoTraderEndpoints::Advertisers->value,
            [],
            array_merge([
            ], $options),
        );
    }
}
