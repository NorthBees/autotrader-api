<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderAdvertStates;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

function fakeStockEndpoint(array $body, int $status = 200): void
{
    Http::preventStrayRequests();
    Http::fake([
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
            'expiry' => now()->addMonth(),
            'access_token' => fake()->uuid,
        ], 200),
        AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'*' => Http::response(
            $body,
            $status,
            ['content_type' => 'application/json']
        ),
    ]);
}

describe('7 December 2026: Truck, Motorhome and Caravan make/model standardisation', function () {

    it('returns a rejected make/model on stock creation without throwing', function (): void {
        fakeStockEndpoint([
            'warnings' => [
                [
                    'type' => 'ERROR',
                    'feature' => 'stock',
                    'message' => 'Rejected due to invalid model: 1234',
                ],
            ],
            'vehicle' => [
                'vehicleType' => 'Truck',
                'make' => 'DAF',
                'model' => '1234',
                'standard' => [
                    'make' => 'DAF',
                    'model' => null,
                ],
            ],
            'metadata' => ['stockId' => 'abc123'],
        ], 201);

        $response = app(AutotraderApi::class)->createStock(123456, [
            'vehicle' => ['vehicleType' => 'Truck', 'make' => 'DAF', 'model' => '1234'],
        ]);

        expect($response['warnings'][0])->toBe([
            'type' => 'ERROR',
            'feature' => 'stock',
            'message' => 'Rejected due to invalid model: 1234',
        ]);
        expect($response['metadata']['stockId'])->toBe('abc123');
        expect($response['vehicle']['model'])->toBe('1234');
        expect($response['vehicle']['standard']['model'])->toBeNull();
    })->group('autotrader-api', 'stock', 'v2.2.0');

    it('returns a rejected make/model on stock update without throwing', function (): void {
        fakeStockEndpoint([
            'warnings' => [
                ['type' => 'ERROR', 'feature' => 'stock', 'message' => 'Rejected due to invalid make: Foo'],
            ],
            'vehicle' => ['make' => 'Foo', 'standard' => ['make' => null]],
            'metadata' => ['stockId' => 'abc123'],
        ]);

        $response = app(AutotraderApi::class)->updateStock(123456, [
            'metadata' => ['stockId' => 'abc123'],
            'vehicle' => ['make' => 'Foo'],
        ]);

        expect($response['warnings'][0]['message'])->toBe('Rejected due to invalid make: Foo');
        expect($response['vehicle']['standard']['make'])->toBeNull();
    })->group('autotrader-api', 'stock', 'v2.2.0');

    it('passes through the corrected standard model and a REJECTED advert status', function (): void {
        fakeStockEndpoint([
            'results' => [
                [
                    'vehicle' => [
                        'make' => 'DAF',
                        'model' => 'XF 450',
                        'standard' => ['make' => 'DAF', 'model' => 'XF'],
                    ],
                    'adverts' => ['retailAdverts' => ['autotraderAdvert' => ['status' => 'PUBLISHED']]],
                ],
                [
                    'vehicle' => [
                        'make' => 'DAF',
                        'model' => '1234',
                        'standard' => ['make' => 'DAF', 'model' => null],
                    ],
                    'adverts' => ['retailAdverts' => ['autotraderAdvert' => ['status' => 'REJECTED']]],
                ],
            ],
        ]);

        $response = app(AutotraderApi::class)->getStockList(123456);

        expect($response['results'][0]['vehicle']['model'])->toBe('XF 450');
        expect($response['results'][0]['vehicle']['standard']['model'])->toBe('XF');
        expect(AutotraderAdvertStates::from($response['results'][1]['adverts']['retailAdverts']['autotraderAdvert']['status']))
            ->toBe(AutotraderAdvertStates::REJECTED);
    })->group('autotrader-api', 'stock', 'v2.2.0');
});
