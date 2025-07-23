<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\Validators\AbstractAutotraderValidator;

// Create a concrete implementation for testing
class TestValidator extends AbstractAutotraderValidator
{
    protected function getRules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'age' => 'integer|min:18'
        ];
    }

    protected function getMessages(): array
    {
        return [
            'name.required' => 'Name is mandatory',
            'email.email' => 'Invalid email format'
        ];
    }
}

describe('AbstractAutotraderValidator', function () {
    beforeEach(function () {
        $this->validator = new TestValidator();
    });

    it('can validate correct data', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25
        ];

        $result = $this->validator->validate($data);

        expect($result)->toBe($data);
    });

    it('throws ValidationException for invalid data', function () {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'age' => 15
        ];

        $this->validator->validate($data);
    })->throws(ValidationException::class);

    it('returns only validated fields', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
            'extra_field' => 'should be removed'
        ];

        $result = $this->validator->validate($data);

        expect($result)->not->toHaveKey('extra_field');
        expect($result)->toHaveKeys(['name', 'email', 'age']);
    });

    it('can validate empty data when no required fields', function () {
        // Create a validator with no required rules
        $validator = new class extends AbstractAutotraderValidator {
            protected function getRules(): array
            {
                return [
                    'optional_field' => 'string'
                ];
            }
        };

        $result = $validator->validate([]);

        expect($result)->toBe([]);
    });

    it('uses custom messages', function () {
        $data = [
            'name' => '',
            'email' => 'invalid-email'
        ];

        try {
            $this->validator->validate($data);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            expect($errors['name'][0])->toBe('Name is mandatory');
            expect($errors['email'][0])->toBe('Invalid email format');
        }
    });
})->group('validators', 'abstract');