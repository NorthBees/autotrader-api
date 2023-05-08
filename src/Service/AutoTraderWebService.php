<?php

namespace NorthBees\AutotraderApi\Service;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use NorthBees\AutotraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutoTraderClientErrorException;
use NorthBees\AutotraderApi\Exceptions\AutoTraderException;
use NorthBees\AutotraderApi\Exceptions\AutoTraderFailedConnectionException;
use NorthBees\AutotraderApi\Exceptions\AutoTraderNoAdvertiserIdException;
use NorthBees\AutotraderApi\Traits\AutoTraderTaxonomyTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutoTraderVehiclesTrait;

class AutoTraderWebService
{
    use AutoTraderValuationsTrait;
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

        throw_if(! Arr::has($data, 'advertiserId') && ! Str::contains($url, '?advertiserId'), AutoTraderNoAdvertiserIdException::class);

        $response = Http::withToken($this->getAuthenticationCode())->withHeaders($headers)->{$method->value}($url, $data);
        if ($response->successful()) {
            return $response->json();
        }

        dd($response->json(), $url, $data);
        throw new AutoTraderException($response->json('message'), $response->json('code'));
    }

    public function getAuthenticationCode()
    {

        if (Cache::has($this->authCacheKey)) {
            return Cache::get($this->authCacheKey);
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
