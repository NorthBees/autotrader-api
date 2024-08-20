<?php

namespace NorthBees\AutoTraderApi\Traits;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;

trait AutoTraderAdvertisersTrait
{
    public function getAdvertisers(array $options =[])
    {

        return $this->performRequestWithoutAdvertiserId(
            HttpMethods::GET,
            AutoTraderEndpoints::Advertisers->value,
            [],
             array_merge([
             ],$options),
        );
    }
}
