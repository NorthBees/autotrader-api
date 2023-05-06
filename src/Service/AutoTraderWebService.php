<?php

namespace NorthBees\AutoTraderApi\Service;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderClientErrorException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderFailedConnectionException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderNoAdvertiserIdException;
use NorthBees\AutoTraderApi\Traits\AutoTraderTaxonomyTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderVehiclesTrait;

class AutoTraderWebService
{
    use AutoTraderVehiclesTrait;
    use AutoTraderTaxonomyTrait;

    protected $authCacheKey = 'autotrader_api_auth';

    protected function getEndpoint()
    {
        return match (config('autotrader.environment')) {
            'production' => AutoTraderEndpoints::ProductionUrl->value,
            default => AutoTraderEndpoints::SandboxUrl->value
        };
    }

    protected function performRequest(HttpMethods $method, string $url, array $headers = [], array $data = [])
    {

        throw_if(! Arr::has($data, 'advertiserId'), AutoTraderNoAdvertiserIdException::class);

        $response = Http::withToken($this->getAuthenticationCode())->withHeaders($headers)->{$method->value}($url, $data);
        if ($response->successful()) {
            return $response->json();
        }

        throw new AutoTraderException($response->json('message'), $response->json('code'));
    }

    public function getAuthenticationCode()
    {

        if (Cache::has($this->authCacheKey)) {
            return $this->authCacheKey;
        }

        $url = implode('/', [$this->getEndpoint(), AutoTraderEndpoints::Authenticate->value]);
        $response = Http::asForm()->post(
            $url,
            [
                'key' => config('autotrader.key'),
                'secret' => config('autotrader.secret'),
            ]
        );

        if ($response->successful()) {
            $expiry = Carbon::parse($response->json('expires'));
            Cache::put($this->authCacheKey, $response->json('access_token'), $expiry);

            return $response->json('access_token');
        }

        if ($response->failed()) {
            throw new AutoTraderFailedConnectionException($response->json('message'), $response->json('code'));
        }
        if ($response->clientError()) {
            throw new AutoTraderClientErrorException($response->json('message'), $response->json('code'));
        }

        throw new AutoTraderException('Unable to connect to Auto Trader');
    }
}
