<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\HttpMethods;

describe('HttpMethods enum', function () {
    it('has correct values', function () {
        expect(HttpMethods::GET->value)->toBe('get');
        expect(HttpMethods::POST->value)->toBe('post');
        expect(HttpMethods::PUT->value)->toBe('put');
        expect(HttpMethods::PATCH->value)->toBe('patch');
        expect(HttpMethods::DELETE->value)->toBe('delete');
    });

    it('can be converted to string', function () {
        expect(HttpMethods::GET->value)->toBe('get');
        expect(HttpMethods::POST->value)->toBe('post');
    });

    it('has all expected cases', function () {
        $cases = HttpMethods::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        expect($values)->toContain('get', 'post', 'put', 'patch', 'delete');
        expect($cases)->toHaveCount(5);
    });
})->group('enum', 'http-methods');