<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\AutotraderNotificationTypes;

describe('Version 1.3.0 API Changes', function () {

    it('exposes the AutotraderNotificationTypes enum', function (): void {
        $values = array_map(fn ($case) => $case->value, AutotraderNotificationTypes::cases());

        expect($values)->toContain('advertiserNotification', 'dealsNotification', 'stockNotification');
    })->group('autotrader-api', 'integrations', 'v1.3.0');

    it('returns financeTerms in the quotes response', function (): void {
        $token = fake()->uuid;
        $mockQuotesResponse = [
            'financeTerms' => [
                'productType' => 'PCP',
                'termMonths' => 36,
                'estimatedAnnualMileage' => 6000,
                'cashPrice' => ['amountGBP' => 15000.0],
                'deposit' => ['amountGBP' => 1000.0],
                'partExchange' => ['amountGBP' => 0.0],
                'outstandingFinance' => ['amountGBP' => 0.0],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Finance->value.'*' => Http::response(
                $mockQuotesResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getFinanceOptions(123456, ['derivativeId' => 'abc']);

        expect($response)->toHaveKey('financeTerms');
        expect($response['financeTerms'])->toHaveKey('productType');
        expect($response['financeTerms']['termMonths'])->toBe(36);
    })->group('autotrader-api', 'finance', 'v1.3.0');

    it('submits an application with existingLoanMonthlyPayment', function (): void {
        $token = fake()->uuid;

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Finance->value.'*' => Http::response(
                ['applicationId' => 'app-789'],
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->submitFinanceApplication(123456, [
            'applicant' => [
                'lastName' => 'Smith',
                'existingLoanMonthlyPayment' => ['amountGBP' => 100],
            ],
        ]);

        expect($response)->toHaveKey('applicationId');

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), AutotraderEndpoints::Finance->value)) {
                return false;
            }

            return data_get($request->data(), 'applicant.existingLoanMonthlyPayment.amountGBP') === 100;
        });
    })->group('autotrader-api', 'finance', 'v1.3.0');

    it('returns notifications in the integrations response', function (): void {
        $token = fake()->uuid;
        $mockIntegrationsResponse = [
            'notifications' => [
                AutotraderNotificationTypes::ADVERTISER->value => [
                    'url' => 'https://example.com/advertiser',
                    'rateLimit' => null,
                    'enabled' => true,
                ],
                AutotraderNotificationTypes::DEALS->value => [
                    'url' => 'https://example.com/deals',
                    'rateLimit' => 100,
                    'enabled' => true,
                ],
                AutotraderNotificationTypes::STOCK->value => [
                    'url' => 'https://example.com/stock',
                    'rateLimit' => 50,
                    'enabled' => true,
                ],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Integrations->value.'*' => Http::response(
                $mockIntegrationsResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getIntegrations();

        expect($response)->toHaveKey('notifications');
        expect($response['notifications'])->toHaveKey(AutotraderNotificationTypes::DEALS->value);
        expect($response['notifications'][AutotraderNotificationTypes::DEALS->value]['rateLimit'])->toBe(100);
    })->group('autotrader-api', 'integrations', 'v1.3.0');

})->group('v1.3.0');
