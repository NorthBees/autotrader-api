<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderClientErrorException;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;
use NorthBees\AutotraderApi\Exceptions\AutotraderFailedConnectionException;

trait AutotraderAuthenticationTrait
{
    protected string $authCacheKey = 'autotrader_api_auth';

    public function getAuthenticationCode()
    {

        if (Cache::has($this->authCacheKey)) {
            return Cache::get($this->authCacheKey);
        }

        $url = implode('/', [$this->getEndpoint(), AutotraderEndpoints::Authenticate->value]);
        $response = Http::asForm()->post(
            $url,
            [
                'key' => config('autotrader.key'),
                'secret' => config('autotrader.secret'),
            ],
        );

        if ($response->successful()) {
            $expiry = Carbon::parse($response->json('expires_at'));
            Cache::put($this->authCacheKey, $response->json('access_token'), $expiry);

            return $response->json('access_token');
        }

        if ($response->failed()) {
            throw new AutotraderFailedConnectionException($response->json('message'), $response->json('code'));
        }
        if ($response->clientError()) {
            throw new AutotraderClientErrorException($response->json('message'), $response->json('code'));
        }

        throw new AutotraderException('Unable to connect to Auto Trader');
    }
}
