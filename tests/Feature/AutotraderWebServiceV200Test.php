<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderMissingOdometerException;
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

describe('Version 2.0.0 API Changes', function () {

    it('returns the results envelope exactly as the API sends it', function (): void {
        $body = getAutotraderFixture('vehicles-results.json');

        fakeEndpoint(AutotraderEndpoints::Vehicles, $body);

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response)->toBe($body);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('no longer flattens the record onto the response root', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response)
            ->toHaveKey('results')
            ->toHaveKey('totalResults', 1)
            ->not->toHaveKey('vehicle')
            ->not->toHaveKey('features')
            ->not->toHaveKey('history');

        expect($response['results'][0]['vehicle']['registration'])->toBe('DC64AGZ');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('no longer adds the serviceWarnings and recordWarnings keys', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response)
            ->not->toHaveKey('serviceWarnings')
            ->not->toHaveKey('recordWarnings');

        // Service level warnings stay at the root, record level ones sit on the record.
        expect($response['warnings'])->toBeArray()
            ->and($response['results'][0]['warnings'][0]['feature'])->toBe('valuations');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('keeps service and record warnings separate once they stop being duplicated', function (): void {
        // From 28 Oct 2026 the root holds service level warnings only.
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results-post-oct.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000);

        expect($response['warnings'])->toHaveCount(1)
            ->and($response['warnings'][0])->not->toHaveKey('feature')
            ->and($response['results'][0]['warnings'])->toHaveCount(1)
            ->and($response['results'][0]['warnings'][0]['feature'])->toBe('valuations');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('sources previous owners from the record history block', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results.json'));

        $record = app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000)['results'][0];

        expect($record['vehicle'])->not->toHaveKey('previousOwners')
            ->and($record['history']['previousOwners'])->toBe(2)
            ->and($record['vehicle']['owners'])->toBe(3);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('returns an empty results array for an unknown registration', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, getAutotraderFixture('vehicles-results-empty.json'));

        $response = app(AutotraderApi::class)->getVehicle(123456, 'zz99zzz', 85000);

        expect($response['results'])->toBe([])
            ->and($response['totalResults'])->toBe(0);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('passes a legacy vehicle root straight through without reshaping it', function (): void {
        // The SDK no longer inspects the response shape, so a pre-28-Oct-2026 payload is
        // returned untouched rather than being adapted.
        $body = getAutotraderFixture('vehicles-legacy.json');

        fakeEndpoint(AutotraderEndpoints::Vehicles, $body);

        expect(app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))->toBe($body);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('leaves the search results array intact', function (): void {
        fakeEndpoint(AutotraderEndpoints::Search, [
            'results' => [['vehicle' => ['registration' => 'DC64AGZ']]],
            'totalResults' => 1,
        ]);

        expect(app(AutotraderApi::class)->searchVehicles(123456)['results'])->toHaveCount(1);
    })->group('autotrader-api', 'search', 'v2.0.0');

    it('leaves the stock results array intact', function (): void {
        fakeEndpoint(AutotraderEndpoints::Stock, [
            'results' => [
                ['vehicle' => ['registration' => 'DC64AGZ']],
                ['vehicle' => ['registration' => 'EF24UXT']],
            ],
            'totalResults' => 2,
        ]);

        expect(app(AutotraderApi::class)->getStockList(123456)['results'])->toHaveCount(2);
    })->group('autotrader-api', 'stock', 'v2.0.0');
});

describe('Version 2.0.0 odometer guard', function () {

    it('allows a basic lookup with no odometer reading', function (): void {
        // Under 1.x the default options threw, because the string 'false' is truthy.
        fakeEndpoint(AutotraderEndpoints::Vehicles, ['results' => [], 'totalResults' => 0]);

        expect(app(AutotraderApi::class)->getVehicle(123456, 'dc64agz'))
            ->toHaveKey('totalResults', 0);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('allows odometer dependent options to be explicitly disabled', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, ['results' => [], 'totalResults' => 0]);

        expect(app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', null, [
            'valuations' => 'false',
            'vehicleMetrics' => 'false',
        ]))->toHaveKey('totalResults', 0);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('requires an odometer reading for valuations', function (): void {
        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', null, ['valuations' => 'true']))
            ->toThrow(AutotraderMissingOdometerException::class);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('requires an odometer reading for vehicle metrics', function (): void {
        // Under 1.x this never threw: the guard checked a 'metrics' key that does not exist.
        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', null, ['vehicleMetrics' => 'true']))
            ->toThrow(AutotraderMissingOdometerException::class);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('accepts a real boolean for an odometer dependent option', function (): void {
        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', null, ['valuations' => true]))
            ->toThrow(AutotraderMissingOdometerException::class);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('does not require an odometer reading once one is supplied', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, ['results' => [], 'totalResults' => 0]);

        expect(app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000, ['valuations' => 'true']))
            ->toHaveKey('totalResults', 0);
    })->group('autotrader-api', 'vehicle', 'v2.0.0');
});

describe('Version 2.0.0 warning handling', function () {

    it('collects record level warnings from an unsuccessful response', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, [
            'results' => [[
                'warnings' => [['feature' => 'valuations', 'message' => 'Cannot source derivative information']],
            ]],
        ], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'Cannot source derivative information');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('does not fatal on a warnings entry without a message key', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, ['warnings' => ['a plain string warning']], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'a plain string warning');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('keeps throwing AutotraderWarning when the warnings array is empty', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, ['warnings' => []], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'An unknown warning occurred');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');

    it('de-duplicates repeated warning messages in the exception', function (): void {
        fakeEndpoint(AutotraderEndpoints::Vehicles, [
            'warnings' => [['message' => 'Cannot source derivative information']],
            'results' => [[
                'warnings' => [['message' => 'Cannot source derivative information']],
            ]],
        ], 400);

        expect(fn () => app(AutotraderApi::class)->getVehicle(123456, 'dc64agz', 85000))
            ->toThrow(AutotraderWarning::class, 'Cannot source derivative information');
    })->group('autotrader-api', 'vehicle', 'v2.0.0');
});
