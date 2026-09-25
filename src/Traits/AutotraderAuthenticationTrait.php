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

    protected ?string $credentialKey = null;

    protected ?string $credentialSecret = null;

    /**
     * Return a copy of this client that authenticates with the given key and secret
     * instead of `autotrader.key` / `autotrader.secret`.
     */
    public function withCredentials(string $key, string $secret): static
    {
        $clone = clone $this;
        $clone->credentialKey = $key;
        $clone->credentialSecret = $secret;

        return $clone;
    }

    public function getAuthenticationCode()
    {
        $cacheKey = $this->getAuthenticationCacheKey();

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $url = implode('/', [$this->getEndpoint(), AutotraderEndpoints::Authenticate->value]);
        $response = Http::asForm()->post(
            $url,
            [
                'key' => $this->getCredentialKey(),
                'secret' => $this->getCredentialSecret(),
            ],
        );

        if ($response->successful()) {
            $expiry = Carbon::parse($response->json('expires_at'));
            Cache::put($cacheKey, $response->json('access_token'), $expiry);

            return $response->json('access_token');
        }

        if ($response->clientError()) {
            throw new AutotraderClientErrorException($response->json('message'), $response->json('code'));
        }
        if ($response->failed()) {
            throw new AutotraderFailedConnectionException($response->json('message'), $response->json('code'));
        }

        throw new AutotraderException('Unable to connect to Autotrader');
    }

    /**
     * The cache key for the access token, unique per endpoint and API key so that
     * different credentials (or sandbox and production) never share a token.
     */
    public function getAuthenticationCacheKey(): string
    {
        return $this->authCacheKey.':'.hash('sha256', $this->getEndpoint().'|'.$this->getCredentialKey());
    }

    protected function getCredentialKey(): string
    {
        return (string) ($this->credentialKey ?? config('autotrader.key'));
    }

    protected function getCredentialSecret(): string
    {
        return (string) ($this->credentialSecret ?? config('autotrader.secret'));
    }
}
