<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderAdvertisersTrait
{
    public function getAdvertisers(array $options = [])
    {

        return $this->performRequestWithoutAdvertiserId(
            HttpMethods::GET,
            AutotraderEndpoints::Advertisers->value,
            [],
            array_merge([
            ], $options),
        );
    }
}
