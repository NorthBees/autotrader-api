<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\Validators\AutotraderCompetitorStockValidator;

describe('AutotraderCompetitorStockValidator', function (): void {

    it('returns an empty array when the input is empty', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $result = $validator->validate([]);

        expect($result)->toBe([]);
    });

    it('returns the validated data when all known filters are provided', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $data = [
            'page' => 1,
            'pageSize' => 20,
            'valuations' => true,
            'standardMake' => 'Volkswagen',
            'standardModel' => 'Passat',
            'standardTrim' => 'SEL',
            'standardTransmissionType' => 'Manual',
            'standardFuelType' => 'Diesel',
            'standardBodyType' => 'Estate',
            'standardDrivetrain' => 'Front Wheel Drive',
            'minPlate' => 20,
            'maxPlate' => 20,
            'minEnginePowerBHP' => 142,
            'maxEnginePowerBHP' => 158,
            'minBadgeEngineSizeLitres' => 2.0,
            'maxBadgeEngineSizeLitres' => 2.0,
            'doors' => 5,
            'registration' => 'KN20FZG',
            'postcode' => 'SW1A 1AA',
            'radius' => 50,
        ];

        $result = $validator->validate($data);

        expect($result)->toMatchArray($data);
    });

    it('strips unknown fields from the validated output', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $data = [
            'standardMake' => 'Ford',
            'unknownField' => 'should-be-removed',
        ];

        $result = $validator->validate($data);

        expect($result)->not->toHaveKey('unknownField');
        expect($result)->toHaveKey('standardMake');
    });

    it('throws a validation exception when pageSize exceeds 20', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $this->expectException(ValidationException::class);
        $validator->validate(['pageSize' => 21]);
    });

    it('throws a validation exception when page exceeds 10', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $this->expectException(ValidationException::class);
        $validator->validate(['page' => 11]);
    });

    it('includes the correct custom message for pageSize violation', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        try {
            $validator->validate(['pageSize' => 25]);
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            expect($e->errors()['pageSize'][0])->toContain('maximum page size of 20');
        }
    });

    it('includes the correct custom message for page violation', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        try {
            $validator->validate(['page' => 15]);
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            expect($e->errors()['page'][0])->toContain('maximum of 10 pages');
        }
    });

    it('allows pageSize at the boundary value of 20', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $result = $validator->validate(['pageSize' => 20]);

        expect($result['pageSize'])->toBe(20);
    });

    it('allows page at the boundary value of 10', function (): void {
        $validator = app(AutotraderCompetitorStockValidator::class);

        $result = $validator->validate(['page' => 10]);

        expect($result['page'])->toBe(10);
    });

})->group('validator', 'competitor-stock');
