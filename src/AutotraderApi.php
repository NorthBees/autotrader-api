<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;
use NorthBees\AutotraderApi\Exceptions\AutotraderNoAdvertiserIdException;
use NorthBees\AutotraderApi\Exceptions\AutotraderWarning;
use NorthBees\AutotraderApi\Traits\AutotraderAdvertisersTrait;
use NorthBees\AutotraderApi\Traits\AutotraderAuthenticationTrait;
use NorthBees\AutotraderApi\Traits\AutotraderCallsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderCoDriverTrait;
use NorthBees\AutotraderApi\Traits\AutotraderDealsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderDeliveryTrait;
use NorthBees\AutotraderApi\Traits\AutotraderFinanceTrait;
use NorthBees\AutotraderApi\Traits\AutotraderFutureValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderHistoricValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderImageTrait;
use NorthBees\AutotraderApi\Traits\AutotraderIntegrationsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderMessagesTrait;
use NorthBees\AutotraderApi\Traits\AutotraderSearchTrait;
use NorthBees\AutotraderApi\Traits\AutotraderStockTrait;
use NorthBees\AutotraderApi\Traits\AutotraderTaxonomyTrait;
use NorthBees\AutotraderApi\Traits\AutotraderValuationsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderVehicleMetricsTrait;
use NorthBees\AutotraderApi\Traits\AutotraderVehiclesTrait;

class AutotraderApi
{
    use AutotraderAdvertisersTrait;
    use AutotraderAuthenticationTrait;
    use AutotraderCallsTrait;
    use AutotraderCoDriverTrait;
    use AutotraderDealsTrait;
    use AutotraderDeliveryTrait;
    use AutotraderFinanceTrait;
    use AutotraderFutureValuationsTrait;
    use AutotraderHistoricValuationsTrait;
    use AutotraderImageTrait;
    use AutotraderIntegrationsTrait;
    use AutotraderMessagesTrait;
    use AutotraderSearchTrait;
    use AutotraderStockTrait;
    use AutotraderTaxonomyTrait;
    use AutotraderValuationsTrait;
    use AutotraderVehicleMetricsTrait;
    use AutotraderVehiclesTrait;

    protected function performRequest(HttpMethods $method, string $url, array $headers = [], array $data = [])
    {
        throw_if(
            ! Arr::has($data, 'advertiserId') && ! Str::contains($url, '?advertiserId'),
            AutotraderNoAdvertiserIdException::class,
        );

        $url = implode('/', [$this->getEndpoint(), $url]);

        $request = Http::withToken($this->getAuthenticationCode())->withHeaders($headers);

        $response = $request->{$method->value}($url, $data);

        if ($response->successful()) {
            return $response->json();
        }

        $this->handleUnsuccessfulResponse($response);
    }

    protected function performRequestWithoutAdvertiserId(
        HttpMethods $method,
        string $url,
        array $headers = [],
        array $data = [],
    ) {
        $url = implode('/', [$this->getEndpoint(), $url]);

        $request = Http::withToken($this->getAuthenticationCode())->withHeaders($headers);
        $response = $request->{$method->value}($url, $data);
        if ($response->successful()) {
            return $response->json();
        }

        return $this->handleUnsuccessfulResponse($response);
    }

    /**
     * Handle an unsuccessful response form the Autotrader API.
     *
     *
     * @throws AutotraderException
     */
    protected function handleUnsuccessfulResponse(Response $response)
    {
        $message = $response->json('warning', $response->json('message'));
        $code = $response->json('code');

        // Most errors return a message and a code
        if ($message !== null && $code !== null) {
            throw new AutotraderException($message, $code);
        }

        // If not, there are likely warnings, so ensure there is a code set
        // and build the message from the warnings

        if ($code === null) {
            $code = $response->status();
        }

        $warnings = $response->json('warnings');
        if ($warnings !== null) {
            $message = collect($warnings)->map(
                fn ($warning) => $warning['message'],
            )->implode('; ');
            throw new AutotraderWarning($message, $code);
        }

        if ($message === null) {
            $message = 'An unknown error occurred';
        }

        throw new AutotraderException($message, $code);
    }

    protected function getEndpoint(): string
    {
        return match (config('autotrader.environment')) {
            'production' => AutotraderEndpoints::ProductionUrl->value,
            default => AutotraderEndpoints::SandboxUrl->value,
        };
    }
}
