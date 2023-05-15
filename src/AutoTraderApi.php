<?php

namespace NorthBees\AutoTraderApi;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderNoAdvertiserIdException;
use NorthBees\AutoTraderApi\Traits\AutoTraderAuthenticationTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderFutureValuationsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderHistoricValuationsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderImageTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderStockTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderTaxonomyTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderValuationsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderVehiclesTrait;

class AutoTraderApi
{
    use AutoTraderAuthenticationTrait;
    use AutoTraderFutureValuationsTrait;
    use AutoTraderHistoricValuationsTrait;
    use AutoTraderImageTrait;
    use AutoTraderStockTrait;
    use AutoTraderTaxonomyTrait;
    use AutoTraderValuationsTrait;
    use AutoTraderVehiclesTrait;

    protected function performRequest(HttpMethods $method, string $url, array $headers = [], array $data = [])
    {
        throw_if(! Arr::has($data, 'advertiserId') && ! Str::contains($url, '?advertiserId'), AutoTraderNoAdvertiserIdException::class);

        $url = implode('/', [$this->getEndpoint(), $url]);

        $request = Http::withToken($this->getAuthenticationCode())->withHeaders($headers);
        $response = $request->{$method->value}($url, $data);
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
