<?php

namespace NorthBees\AutotraderApi;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use NorthBees\AutotraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutoTraderException;
use NorthBees\AutotraderApi\Exceptions\AutoTraderNoAdvertiserIdException;
use NorthBees\AutotraderApi\Traits\AutoTraderAuthenticationTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderFutureValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderHistoricValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderTaxonomyTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderVehiclesTrait;

class AutotraderApi
{
    use AutoTraderAuthenticationTrait;
    use AutoTraderFutureValuationsTrait;
    use AutoTraderHistoricValuationsTrait;
    use AutoTraderValuationsTrait;
    use AutoTraderVehiclesTrait;
    use AutoTraderTaxonomyTrait;

    protected function performRequest(HttpMethods $method, string $url, array $headers = [], array $data = [])
    {
        throw_if(! Arr::has($data, 'advertiserId') && ! Str::contains($url, '?advertiserId'), AutoTraderNoAdvertiserIdException::class);

        $url = implode('/', [$this->getEndpoint(), $url]);

        $response = Http::withToken($this->getAuthenticationCode())->withHeaders($headers)->{$method->value}($url, $data);
        if ($response->successful()) {
            return $response->json();
        }

        throw new AutoTraderException($response->json('message'), $response->json('code'));
    }

    protected function getEndpoint()
    {
        return match (config('autotrader.environment')) {
            'production' => AutoTraderEndpoints::ProductionUrl->value,
            default => AutoTraderEndpoints::SandboxUrl->value
        };
    }
}
