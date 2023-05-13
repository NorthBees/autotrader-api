<?php

namespace NorthBees\AutoTraderApi\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderClientErrorException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderFailedConnectionException;

trait AutoTraderAuthenticationTrait
{
    protected string $authCacheKey = 'autotrader_api_auth';

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
