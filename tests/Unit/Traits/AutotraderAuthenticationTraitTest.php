<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;
use NorthBees\AutotraderApi\Exceptions\AutotraderFailedConnectionException;
use NorthBees\AutotraderApi\Exceptions\AutotraderClientErrorException;
use NorthBees\AutotraderApi\Traits\AutotraderAuthenticationTrait;

// Create a test class that uses the trait
class TestClassWithAuthTrait
{
    use AutotraderAuthenticationTrait;

    protected function getEndpoint(): string
    {
        return 'https://api-sandbox.autotrader.co.uk';
    }
}

describe('AutotraderAuthenticationTrait', function () {
    beforeEach(function () {
        $this->testInstance = new TestClassWithAuthTrait();
        Cache::flush(); // Clear cache before each test
    });

    it('returns cached token when available', function () {
        $cachedToken = 'cached-token-123';
        Cache::put('autotrader_api_auth', $cachedToken, now()->addHour());

        $result = $this->testInstance->getAuthenticationCode();

        expect($result)->toBe($cachedToken);
    });

    it('fetches new token when cache is empty', function () {
        $newToken = 'new-token-456';
        
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.autotrader.co.uk/authenticate' => Http::response([
                'access_token' => $newToken,
                'expires_at' => now()->addMonth()->toISOString(),
            ], 200),
        ]);

        $result = $this->testInstance->getAuthenticationCode();

        expect($result)->toBe($newToken);
        expect(Cache::get('autotrader_api_auth'))->toBe($newToken);
    });

    it('throws AutotraderFailedConnectionException on failed response', function () {
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.autotrader.co.uk/authenticate' => Http::response([
                'message' => 'Connection failed',
                'code' => 500,
            ], 500),
        ]);

        $this->testInstance->getAuthenticationCode();
    })->throws(AutotraderFailedConnectionException::class);

    it('throws AutotraderClientErrorException on client error', function () {
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.autotrader.co.uk/authenticate' => Http::response([
                'message' => 'Unauthorized',
                'code' => 401,
            ], 401),
        ]);

        $this->testInstance->getAuthenticationCode();
    })->throws(AutotraderClientErrorException::class);

    it('throws AutotraderException for other errors', function () {
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.autotrader.co.uk/authenticate' => Http::response([], 999),
        ]);

        $this->testInstance->getAuthenticationCode();
    })->throws(AutotraderException::class);
})->group('traits', 'authentication');