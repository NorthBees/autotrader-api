<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\VehicleTypes;

describe('VehicleTypes enum', function () {
    it('has correct values', function () {
        expect(VehicleTypes::Car->value)->toBe('Car');
        expect(VehicleTypes::Bike->value)->toBe('Bike');
        expect(VehicleTypes::Van->value)->toBe('Van');
        expect(VehicleTypes::Truck->value)->toBe('Truck');
    });

    it('has all vehicle types', function () {
        $cases = VehicleTypes::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        expect($values)->toContain(
            'Bike', 'Car', 'Caravan', 'Crossover', 
            'Farm', 'Motorhome', 'Plant', 'Truck', 'Van'
        );
        expect($cases)->toHaveCount(9);
    });

    it('can be used in string context', function () {
        expect((string) VehicleTypes::Car)->toBe('Car');
        expect((string) VehicleTypes::Bike)->toBe('Bike');
    });
})->group('enum', 'vehicle-types');