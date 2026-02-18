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
        $values = array_map(fn ($case) => $case->value, $cases);

        expect($values)->toContain('PUBLISHED', 'NOT_PUBLISHED');
        expect($cases)->toHaveCount(2);
    });

    it('can access value property', function () {
        expect(AutotraderTradeAdvertStates::PUBLISHED->value)->toBe('PUBLISHED');
        expect(AutotraderTradeAdvertStates::NOT_PUBLISHED->value)->toBe('NOT_PUBLISHED');
    });
})->group('enum', 'trade-advert-states');
