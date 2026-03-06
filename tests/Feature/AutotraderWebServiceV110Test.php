<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

describe('Version 1.1.0 API Changes', function () {

    it('has getFinanceApplication method available', function (): void {
        expect(method_exists(app(AutotraderApi::class), 'getFinanceApplication'))->toBeTrue();
    })->group('autotrader-api', 'finance', 'v1.1.0');

    it('can get a finance application', function (): void {
        $token = fake()->uuid;
        $mockApplicationResponse = [
            'applicationId' => 'app-123',
            'status' => 'Submitted',
            'financeTerms' => [
                'productType' => 'PCP',
                'term' => 48,
            ],
            'applicant' => [
                'lastName' => 'Smith',
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Finance->value.'/*' => Http::response(
                $mockApplicationResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getFinanceApplication(123456, 'app-123');

        expect($response)->toHaveKey('applicationId');
        expect($response)->toHaveKey('status');
        expect($response['status'])->toBe('Submitted');
    })->group('autotrader-api', 'finance', 'v1.1.0');

    it('can get an anonymised finance application with Expired status', function (): void {
        $token = fake()->uuid;
        $mockAnonymisedResponse = [
            'applicationId' => 'app-456',
            'status' => 'Expired',
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Finance->value.'/*' => Http::response(
                $mockAnonymisedResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getFinanceApplication(123456, 'app-456');

        expect($response)->toHaveKey('applicationId');
        expect($response)->toHaveKey('status');
        expect($response['applicationId'])->toBe('app-456');
        expect($response['status'])->toBe('Expired');
        expect($response)->toHaveCount(2);
    })->group('autotrader-api', 'finance', 'v1.1.0');

})->group('v1.1.0');
