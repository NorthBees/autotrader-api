<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

beforeEach(function (): void {
    Cache::flush();
    Http::preventStrayRequests();
});

function fakeAutotraderAuthentication(string $baseUrl = AutotraderEndpoints::SandboxUrl->value): void
{
    Http::fake([
        $baseUrl.'/'.AutotraderEndpoints::Authenticate->value => fn (Request $request) => Http::response([
            'access_token' => 'token-for-'.$request['key'],
            'expires_at' => now()->addHour()->toISOString(),
        ]),
    ]);
}

it('authenticates with the configured credentials by default', function (): void {
    fakeAutotraderAuthentication();

    expect(app(AutotraderApi::class)->getAuthenticationCode())->toBe('token-for-test-key');

    Http::assertSent(fn (Request $request): bool => $request['key'] === 'test-key' && $request['secret'] === 'test-secret');
});

it('authenticates with per-instance credentials when given', function (): void {
    fakeAutotraderAuthentication();

    $api = app(AutotraderApi::class)->withCredentials('tenant-key', 'tenant-secret');

    expect($api->getAuthenticationCode())->toBe('token-for-tenant-key');

    Http::assertSent(fn (Request $request): bool => $request['key'] === 'tenant-key' && $request['secret'] === 'tenant-secret');
});

it('returns a copy and leaves the original client on the configured credentials', function (): void {
    fakeAutotraderAuthentication();

    $api = new AutotraderApi;
    $tenantApi = $api->withCredentials('tenant-key', 'tenant-secret');

    expect($tenantApi)->not->toBe($api)
        ->and($api->getAuthenticationCode())->toBe('token-for-test-key')
        ->and($tenantApi->getAuthenticationCode())->toBe('token-for-tenant-key');
});

it('caches tokens separately per API key', function (): void {
    fakeAutotraderAuthentication();

    $first = (new AutotraderApi)->withCredentials('first-key', 'first-secret');
    $second = (new AutotraderApi)->withCredentials('second-key', 'second-secret');

    expect($first->getAuthenticationCacheKey())->not->toBe($second->getAuthenticationCacheKey())
        ->and($first->getAuthenticationCode())->toBe('token-for-first-key')
        ->and($second->getAuthenticationCode())->toBe('token-for-second-key')
        ->and($first->getAuthenticationCode())->toBe('token-for-first-key')
        ->and($second->getAuthenticationCode())->toBe('token-for-second-key');

    Http::assertSentCount(2);
});

it('does not expose the API key in the cache key', function (): void {
    expect((new AutotraderApi)->withCredentials('tenant-key', 'tenant-secret')->getAuthenticationCacheKey())
        ->not->toContain('tenant-key');
});

it('targets the given environment instead of the configured one', function (): void {
    fakeAutotraderAuthentication(AutotraderEndpoints::ProductionUrl->value);

    $api = (new AutotraderApi)->withEnvironment('production');

    expect($api->getAuthenticationCode())->toBe('token-for-test-key');

    Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), AutotraderEndpoints::ProductionUrl->value));
});

it('caches sandbox and production tokens separately for the same key', function (): void {
    expect((new AutotraderApi)->getAuthenticationCacheKey())
        ->not->toBe((new AutotraderApi)->withEnvironment('production')->getAuthenticationCacheKey());
});
