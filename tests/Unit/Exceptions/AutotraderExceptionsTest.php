<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Exceptions\AutotraderClientErrorException;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;
use NorthBees\AutotraderApi\Exceptions\AutotraderFailedConnectionException;
use NorthBees\AutotraderApi\Exceptions\AutotraderMissingOdometerException;
use NorthBees\AutotraderApi\Exceptions\AutotraderNoAdvertiserIdException;
use NorthBees\AutotraderApi\Exceptions\AutotraderWarning;

describe('AutotraderException', function () {
    it('can be instantiated', function () {
        $exception = new AutotraderException('Test message', 123);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception->getMessage())->toBe('Test message');
        expect($exception->getCode())->toBe(123);
    });

    it('extends base Exception', function () {
        expect(new AutotraderException)->toBeInstanceOf(Exception::class);
    });
});

describe('AutotraderNoAdvertiserIdException', function () {
    it('extends AutotraderException', function () {
        $exception = new AutotraderNoAdvertiserIdException;

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception)->toBeInstanceOf(AutotraderNoAdvertiserIdException::class);
    });
});

describe('AutotraderClientErrorException', function () {
    it('can store client error details', function () {
        $exception = new AutotraderClientErrorException('Client error', 400);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception->getMessage())->toBe('Client error');
        expect($exception->getCode())->toBe(400);
    });
});

describe('AutotraderFailedConnectionException', function () {
    it('can store connection failure details', function () {
        $exception = new AutotraderFailedConnectionException('Connection failed', 500);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception->getMessage())->toBe('Connection failed');
        expect($exception->getCode())->toBe(500);
    });
});

describe('AutotraderWarning', function () {
    it('can store warning details', function () {
        $exception = new AutotraderWarning('Warning message', 200);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception->getMessage())->toBe('Warning message');
        expect($exception->getCode())->toBe(200);
    });
});

describe('AutotraderMissingOdometerException', function () {
    it('extends base exception', function () {
        $exception = new AutotraderMissingOdometerException('Missing odometer');

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception->getMessage())->toBe('Missing odometer');
    });
})->group('exceptions', 'autotrader');
