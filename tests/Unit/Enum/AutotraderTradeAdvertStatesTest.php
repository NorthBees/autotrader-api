<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\AutotraderTradeAdvertStates;

describe('AutotraderTradeAdvertStates enum', function () {
    it('has correct values', function () {
        expect(AutotraderTradeAdvertStates::PUBLISHED->value)->toBe('PUBLISHED');
        expect(AutotraderTradeAdvertStates::NOT_PUBLISHED->value)->toBe('NOT_PUBLISHED');
    });

    it('has all expected trade advert states', function () {
        $cases = AutotraderTradeAdvertStates::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        expect($values)->toContain('PUBLISHED', 'NOT_PUBLISHED');
        expect($cases)->toHaveCount(2);
    });

    it('can be used in string context', function () {
        expect((string) AutotraderTradeAdvertStates::PUBLISHED)->toBe('PUBLISHED');
        expect((string) AutotraderTradeAdvertStates::NOT_PUBLISHED)->toBe('NOT_PUBLISHED');
    });
})->group('enum', 'trade-advert-states');
