<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\AutotraderTaxonomies;

describe('AutotraderTaxonomies enum', function () {
    it('has correct basic taxonomy values', function () {
        expect(AutotraderTaxonomies::VEHICLETYPES->value)->toBe('vehicleTypes');
        expect(AutotraderTaxonomies::MAKES->value)->toBe('makes');
        expect(AutotraderTaxonomies::MODELS->value)->toBe('models');
        expect(AutotraderTaxonomies::GENERATIONS->value)->toBe('generations');
        expect(AutotraderTaxonomies::DERIVATIVES->value)->toBe('derivatives');
    });

    it('has correct advanced taxonomy values', function () {
        expect(AutotraderTaxonomies::FUELTYPES->value)->toBe('fuelTypes');
        expect(AutotraderTaxonomies::TRANSMISSIONTYPES->value)->toBe('transmissionTypes');
        expect(AutotraderTaxonomies::BODYTYPES->value)->toBe('bodyTypes');
        expect(AutotraderTaxonomies::DRIVETRAINS->value)->toBe('drivetrains');
    });

    it('has all expected taxonomy cases', function () {
        $cases = AutotraderTaxonomies::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        expect($values)->toContain(
            'vehicleTypes', 'makes', 'models', 'generations', 'derivatives',
            'features', 'prices', 'fuelTypes', 'transmissionTypes', 'bodyTypes'
        );
        expect($cases)->toHaveCount(21); // All the cases defined in the enum
    });

    it('can be used in string context', function () {
        expect(AutotraderTaxonomies::VEHICLETYPES->value)->toBe('vehicleTypes');
        expect(AutotraderTaxonomies::MAKES->value)->toBe('makes');
    });
})->group('enum', 'taxonomies');