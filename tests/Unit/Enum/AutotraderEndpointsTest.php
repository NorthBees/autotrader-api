<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

describe('AutotraderEndpoints enum', function () {
    it('has correct URL values', function () {
        expect(AutotraderEndpoints::SandboxUrl->value)->toBe('https://api-sandbox.autotrader.co.uk');
        expect(AutotraderEndpoints::ProductionUrl->value)->toBe('https://api.autotrader.co.uk'); // Updated to correct production URL
    });

    it('has correct endpoint values', function () {
        expect(AutotraderEndpoints::Authenticate->value)->toBe('authenticate');
        expect(AutotraderEndpoints::Vehicles->value)->toBe('vehicles');
        expect(AutotraderEndpoints::Taxonomy->value)->toBe('taxonomy');
        expect(AutotraderEndpoints::Stock->value)->toBe('stock');
        expect(AutotraderEndpoints::Valuations->value)->toBe('valuations');
    });

    it('has all expected endpoint cases', function () {
        $cases = AutotraderEndpoints::cases();
        $values = array_map(fn ($case) => $case->value, $cases);

        expect($values)->toContain(
            'authenticate', 'vehicles', 'taxonomy', 'stock',
            'images', 'search', 'valuations', 'future-valuations',
            'historic-valuations', 'vehicle-metrics', 'advertisers',
            'co-driver/stock', 'finance', 'integrations'
        );
    });
})->group('enum', 'endpoints');
