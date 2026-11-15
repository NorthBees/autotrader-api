<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\AutotraderAdvertStates;

describe('AutotraderAdvertStates enum', function () {
    it('has correct values', function () {
        expect(AutotraderAdvertStates::PUBLISHED->value)->toBe('PUBLISHED');
        expect(AutotraderAdvertStates::NOT_PUBLISHED->value)->toBe('NOT_PUBLISHED');
        expect(AutotraderAdvertStates::REJECTED->value)->toBe('REJECTED');
    });

    it('has all expected advert states', function () {
        expect(AutotraderAdvertStates::cases())->toHaveCount(3);
    });

    it('resolves REJECTED from an API status string', function () {
        expect(AutotraderAdvertStates::tryFrom('REJECTED'))->toBe(AutotraderAdvertStates::REJECTED);
    });
})->group('enum', 'advert-states');
