<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\AutotraderApi;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

it('can request stock list', function (): void {

    $token = fake()->uuid;
    $mockStockResponse = [
        'results' => [
            [
                'metadata' => [
                    'lifecycleState' => 'FORECOURT',
                ],
            ],
        ],
        'totalResults' => 1,
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

    $response = app(AutotraderApi::class)->getStockList(123456, ['lifecycleState' => 'FORECOURT']);
    expect($response)->toHaveKey('results')->toHaveKey('totalResults');
    expect(Arr::get($response, 'results.0.metadata.lifecycleState'))->toEqual('FORECOURT');

})->group('autotrader-api', 'stock');

it('can request stock list with competitors option', function (): void {

    $token = fake()->uuid;
    $mockStockResponse = [
        'results' => [
            [
                'metadata' => ['stockId' => 'ABC123'],
                'links' => [
                    'competitors' => [
                        'href' => 'https://api-sandbox.autotrader.co.uk/stock?searchType=competitor&valuations=true&advertiserId=123456&standardMake=Volkswagen',
                    ],
                ],
            ],
        ],
        'totalResults' => 1,
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

    $response = app(AutotraderApi::class)->getStockList(123456, [], ['competitors' => 'true']);
    expect($response)->toHaveKey('results');
    expect(Arr::get($response, 'results.0.links.competitors.href'))->toBeString();

})->group('autotrader-api', 'stock');

describe('getCompetitorStock', function (): void {

    beforeEach(function (): void {
        $this->token = fake()->uuid;
        $this->mockCompetitorResponse = [
            'results' => [
                [
                    'vehicle' => [
                        'make' => 'Volkswagen',
                        'model' => 'Passat',
                    ],
                    'adverts' => [
                        'retailAdverts' => [
                            'totalPrice' => 15000,
                        ],
                    ],
                ],
            ],
            'totalResults' => 1,
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $this->token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'*' => Http::response(
                $this->mockCompetitorResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);
    });

    it('can search competitor stock with valid filters', function (): void {
        $response = app(AutotraderApi::class)->getCompetitorStock(123456, [
            'standardMake' => 'Volkswagen',
            'standardModel' => 'Passat',
            'standardTrim' => 'SEL',
            'minPlate' => 20,
            'maxPlate' => 20,
            'minEnginePowerBHP' => 142,
            'maxEnginePowerBHP' => 158,
            'standardTransmissionType' => 'Manual',
            'standardFuelType' => 'Diesel',
            'standardBodyType' => 'Estate',
            'standardDrivetrain' => 'Front Wheel Drive',
            'doors' => 5,
            'minBadgeEngineSizeLitres' => 2.0,
            'maxBadgeEngineSizeLitres' => 2.0,
        ]);

        expect($response)->toHaveKey('results');
        expect(Arr::get($response, 'results.0.vehicle.make'))->toEqual('Volkswagen');
    });

    it('can search competitor stock with valuations option', function (): void {
        $response = app(AutotraderApi::class)->getCompetitorStock(123456, [
            'standardMake' => 'Ford',
        ], [
            'valuations' => true,
            'page' => 1,
            'pageSize' => 10,
        ]);

        expect($response)->toHaveKey('results');
    });

    it('throws a validation exception when pageSize exceeds 20', function (): void {
        app(AutotraderApi::class)->getCompetitorStock(123456, [], ['pageSize' => 21]);
    })->throws(ValidationException::class);

    it('throws a validation exception when page exceeds 10', function (): void {
        app(AutotraderApi::class)->getCompetitorStock(123456, [], ['page' => 11]);
    })->throws(ValidationException::class);

    it('can exclude a registration using the registration filter', function (): void {
        $response = app(AutotraderApi::class)->getCompetitorStock(123456, [
            'standardMake' => 'Volkswagen',
            'registration' => 'KN20FZG',
        ]);

        expect($response)->toHaveKey('results');
    });

})->group('autotrader-api', 'stock', 'competitor-stock');

describe('getCompetitorStockFromUrl', function (): void {

    beforeEach(function (): void {
        $this->token = fake()->uuid;
        $this->mockCompetitorResponse = [
            'results' => [
                [
                    'vehicle' => [
                        'make' => 'Volkswagen',
                        'model' => 'Passat',
                    ],
                ],
            ],
            'totalResults' => 1,
        ];

        Http::preventStrayRequests();
        Http::fake([
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Authenticate->value => Http::response([
                'expiry' => now()->addMonth(),
                'access_token' => $this->token,
            ], 200),
            AutotraderEndpoints::SandboxUrl->value.'/'.AutotraderEndpoints::Stock->value.'*' => Http::response(
                $this->mockCompetitorResponse,
                200,
                ['content_type' => 'application/json']
            ),
        ]);
    });

    it('can execute a pre-built competitor href URL', function (): void {
        $href = 'https://api-sandbox.autotrader.co.uk/stock?searchType=competitor&valuations=true&advertiserId=123456&standardMake=Volkswagen&standardModel=Passat&minPlate=20&maxPlate=20';

        $response = app(AutotraderApi::class)->getCompetitorStockFromUrl($href);

        expect($response)->toHaveKey('results');
        expect(Arr::get($response, 'results.0.vehicle.make'))->toEqual('Volkswagen');
    });

    it('throws an AutotraderException when the href has no advertiserId', function (): void {
        $href = 'https://api-sandbox.autotrader.co.uk/stock?searchType=competitor&standardMake=Volkswagen';

        app(AutotraderApi::class)->getCompetitorStockFromUrl($href);
    })->throws(AutotraderException::class, 'The competitor href URL must contain an advertiserId parameter.');

})->group('autotrader-api', 'stock', 'competitor-stock');
