<?php

use Illuminate\Support\Arr;
use NorthBees\AutotraderApi\Service\AutoTraderWebService;

todo('mock requests');
it('can request taxonomy data', function () {

    $response = app(AutoTraderWebService::class)->getVehicleTypes(config('autotrader.default_advertiser_id'));
    expect($response)->toHaveKey('vehicleTypes');

    $response = app(AutoTraderWebService::class)->getMakes(config('autotrader.default_advertiser_id'), \NorthBees\AutotraderApi\Enum\VehicleTypes::Car, 'current');
    expect($response)->toHaveKey('makes');

    $this->makeId = collect(Arr::get($response, 'makes'))->where('name', 'Audi')->first()['makeId'];
    $response = app(AutoTraderWebService::class)->getModels(config('autotrader.default_advertiser_id'), $this->makeId, null, 'current');
    expect($response)->toHaveKey('models');

    $model = Arr::random(Arr::get($response, 'models'))['modelId'];
    $response = app(AutoTraderWebService::class)->getGenerations(config('autotrader.default_advertiser_id'), $model, 'current');
    expect($response)->toHaveKey('generations');

    $generation = Arr::random(Arr::get($response, 'generations'))['generationId'];
    $response = app(AutoTraderWebService::class)->getDerivatives(config('autotrader.default_advertiser_id'), $generation, 'current');
    expect($response)->toHaveKey('derivatives');

    $derivative = Arr::random(Arr::get($response, 'derivatives'))['derivativeId'];
    $response = app(AutoTraderWebService::class)->getFeatures(config('autotrader.default_advertiser_id'), $derivative, now()->subYear(), 'current');
    expect($response)->toHaveKey('features');

    $response = app(AutoTraderWebService::class)->getPrices(config('autotrader.default_advertiser_id'), $derivative, null, 'current');
    expect($response)->toHaveKey('prices');

    $response = app(AutoTraderWebService::class)->getTechnicalData(config('autotrader.default_advertiser_id'), $derivative, 'current');
    expect($response)->toHaveKey('derivativeId');

})->group('autotrader-api', 'taxonomy');
