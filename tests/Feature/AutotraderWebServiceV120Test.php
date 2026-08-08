<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderWarning;

/**
 * Fake authentication plus one endpoint.
 */
function fakeEndpoint(AutotraderEndpoints $endpoint, mixed $body, int $status = 200): void
{
    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => fake()->uuid,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.$endpoint->value.'*' => Http::response(
            $body,
            $status,
            ['content_type' => 'application/json']
        ),
    ]);
}

describe('Version 1.2.0 API Changes', function () {

    it('flattens the new results envelope back to the historic shape', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response)
            ->toHaveKey('vehicle')
            ->toHaveKey('features')
            ->toHaveKey('motTests')
            ->toHaveKey('chargeTimes')
            ->toHaveKey('check')
            ->toHaveKey('history')
            ->toHaveKey('totalResults', 1)
            ->not->toHaveKey('results');

        expect($response['vehicle']['registration'])->toBe('DC64AGZ');
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('still accepts the legacy vehicle root until it is withdrawn', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-legacy.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response)->toBe(getAutotraderFixture('vehicles-legacy.json'));
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('sources previous owners from history rather than the withdrawn vehicle field', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response['vehicle'])->not->toHaveKey('previousOwners')
            ->and($response['history']['previousOwners'])->toBe(2)
            ->and($response['vehicle']['owners'])->toBe(3);
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('exposes the service and record warning split', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response['serviceWarnings'])->toHaveCount(1)
            ->and($response['recordWarnings'])->toHaveCount(1)
            ->and($response['warnings'])->toHaveCount(2);
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('returns no vehicle key for an unknown registration', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results-empty.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'zz99zzz', 85000);

        expect($response)
            ->not->toHaveKey('vehicle')
            ->toHaveKey('totalResults', 0);
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('leaves the search results array intact', function (): void {
        fakeEndpoint(AutotraderEndpoints::Search, [
            'results' => [['vehicle' => ['registration' => 'DC64AGZ']]],
            'totalResults' => 1,
        ]);

        $response = app(AutotraderApi::class)->searchVehicles(123456);

        expect($response)->toHaveKey('results')
            ->and($response['results'])->toHaveCount(1);
    })->group('autotrader-api', 'search', 'v1.2.0');

    it('leaves the stock results array intact', function (): void {
        fakeEndpoint(AutotraderEndpoints::Stock, [
            'results' => [
                ['vehicle' => ['registration' => 'DC64AGZ']],
                ['vehicle' => ['registration' => 'EF24UXT']],
            ],
            'totalResults' => 2,
        ]);

        $response = app(AutotraderApi::class)->getStockList(123456);

        expect($response)->toHaveKey('results')
            ->and($response['results'])->toHaveCount(2);
    })->group('autotrader-api', 'stock', 'v1.2.0');

    it('collects record level warnings from an unsuccessful response', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, [
            'results' => [[
                'warnings' => [['feature' => 'valuations', 'message' => 'Cannot source derivative information']],
            ]],
        ], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'Cannot source derivative information');
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('does not fatal on a warnings entry without a message key', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, [
            'warnings' => ['a plain string warning'],
        ], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'a plain string warning');
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('keeps throwing AutotraderWarning when the warnings array is empty', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, ['warnings' => []], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'An unknown warning occurred');
    })->group('autotrader-api', 'vehicle', 'v1.2.0');

    it('de-duplicates repeated warning messages in the exception', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, [
            'warnings' => [['message' => 'Cannot source derivative information']],
            'results' => [[
                'warnings' => [['message' => 'Cannot source derivative information']],
            ]],
        ], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'Cannot source derivative information');
    })->group('autotrader-api', 'vehicle', 'v1.2.0');
});
