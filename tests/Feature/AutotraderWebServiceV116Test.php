<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;

describe('Version 1.1.6 API Changes', function () {

    it('returns the natural and paid responseMetrics breakdown in the stock list response', function (): void {
        $token = fake()->uuid;
        $mockStockResponse = [
            'results' => [
                [
                    'vehicle' => [
                        'responseMetrics' => [
                            'yesterday' => [
                                'advertViews' => 114,
                                'naturalAdvertViews' => 44,
                                'paidPPCAdvertViews' => 70,
                                'searchViews' => 169,
                                'naturalSearchViews' => 69,
                                'paidPPCSearchViews' => 100,
                            ],
                            'lastWeek' => [
                                'advertViews' => 549,
                                'naturalAdvertViews' => 244,
                                'paidPPCAdvertViews' => 305,
                                'searchViews' => 722,
                                'naturalSearchViews' => 316,
                                'paidPPCSearchViews' => 406,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'*' => Http::response(
                $mockStockResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        $response = app(AutotraderApi::class)->getStockList(123456, [], [
            'responseMetrics' => 'true',
        ]);

        $metrics = $response['results'][0]['vehicle']['responseMetrics'];

        expect($metrics['yesterday'])->toHaveKeys([
            'advertViews', 'naturalAdvertViews', 'paidPPCAdvertViews',
            'searchViews', 'naturalSearchViews', 'paidPPCSearchViews',
        ]);
        expect($metrics['lastWeek'])->toHaveKeys([
            'advertViews', 'naturalAdvertViews', 'paidPPCAdvertViews',
            'searchViews', 'naturalSearchViews', 'paidPPCSearchViews',
        ]);

        // Natural and paid views should sum to the existing totals
        expect($metrics['yesterday']['naturalAdvertViews'] + $metrics['yesterday']['paidPPCAdvertViews'])
            ->toBe($metrics['yesterday']['advertViews']);
        expect($metrics['yesterday']['naturalSearchViews'] + $metrics['yesterday']['paidPPCSearchViews'])
            ->toBe($metrics['yesterday']['searchViews']);
        expect($metrics['lastWeek']['naturalAdvertViews'] + $metrics['lastWeek']['paidPPCAdvertViews'])
            ->toBe($metrics['lastWeek']['advertViews']);
        expect($metrics['lastWeek']['naturalSearchViews'] + $metrics['lastWeek']['paidPPCSearchViews'])
            ->toBe($metrics['lastWeek']['searchViews']);
    })->group('autotrader-api', 'stock', 'v1.1.6');

    it('requests responseMetrics when enabled on the stock list', function (): void {
        $token = fake()->uuid;

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'*' => Http::response(
                ['results' => []],
                200,
                ['content_type' => 'application/json']
            ),
        ]);

        app(AutotraderApi::class)->getStockList(123456, [], [
            'responseMetrics' => 'true',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'responseMetrics=true');
        });
    })->group('autotrader-api', 'stock', 'v1.1.6');

})->group('v1.1.6');
