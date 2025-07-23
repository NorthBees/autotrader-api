<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\AutotraderLifecycleStates;

describe('AutotraderLifecycleStates enum', function () {
    it('has correct values', function () {
        expect(AutotraderLifecycleStates::DUE_IN->value)->toBe('DUE_IN');
        expect(AutotraderLifecycleStates::FORECOURT->value)->toBe('FORECOURT');
        expect(AutotraderLifecycleStates::SALE_IN_PROGRESS->value)->toBe('SALE_IN_PROGRESS');
        expect(AutotraderLifecycleStates::WASTEBIN->value)->toBe('WASTEBIN');
        expect(AutotraderLifecycleStates::DELETED->value)->toBe('DELETED');
        expect(AutotraderLifecycleStates::SOLD->value)->toBe('SOLD');
    });

    it('has all expected lifecycle states', function () {
        $cases = AutotraderLifecycleStates::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        expect($values)->toContain(
            'DUE_IN', 'FORECOURT', 'SALE_IN_PROGRESS', 
            'WASTEBIN', 'DELETED', 'SOLD'
        );
        expect($cases)->toHaveCount(6);
    });

    it('can be used in string context', function () {
        expect((string) AutotraderLifecycleStates::FORECOURT)->toBe('FORECOURT');
        expect((string) AutotraderLifecycleStates::SOLD)->toBe('SOLD');
    });
})->group('enum', 'lifecycle-states');