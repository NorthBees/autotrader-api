<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Enum\HttpMethods;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderNoAdvertiserIdException;
use NorthBees\AutoTraderApi\Traits\AutoTraderAdvertisersTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderAuthenticationTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderFutureValuationsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderHistoricValuationsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderImageTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderStockTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderTaxonomyTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderValuationsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderVehicleMetricsTrait;
use NorthBees\AutoTraderApi\Traits\AutoTraderVehiclesTrait;

class AutoTraderApi
{
    use AutoTraderAuthenticationTrait;
    use AutoTraderFutureValuationsTrait;
    use AutoTraderHistoricValuationsTrait;
    use AutoTraderImageTrait;
    use AutoTraderStockTrait;
    use AutoTraderTaxonomyTrait;
    use AutoTraderValuationsTrait;
    use AutoTraderVehiclesTrait;
    use AutoTraderAdvertisersTrait;
    use AutoTraderVehicleMetricsTrait;

    protected function performRequest(HttpMethods $method, string $url, array $headers = [], array $data = [])
    {
        throw_if(
            ! Arr::has($data, 'advertiserId') && ! Str::contains($url, '?advertiserId'),
            AutoTraderNoAdvertiserIdException::class,
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

        $this->handleUnsuccessfulResponse($response);
    }

    /**
     * Handle an unsuccessful response form the AutoTrader API.
     *
     * @param Response $response
     *
     * @throws AutoTraderException
     */
    protected function handleUnsuccessfulResponse(Response $response): void
    {
        $message = $response->json('message');
        $code = $response->json('code');

        // Most errors return a message and a code
        if ($message !== null && $code !== null) {
            throw new AutoTraderException($message, $code);
        }

        // If not, there are likely warnings, so ensure there is a code set
        // and build the message from the warnings

        if ($code === null) {
            $code = $response->getStatusCode();
        }

        $warnings = $response->json('warnings');
        if ($warnings !== null) {
            $message = collect($warnings)->map(
                fn($warning) => $warning['message'],
            )->implode('; ');
        }

        if ($message === null) {
            $message = 'An unknown error occurred';
        }

        throw new AutoTraderException($message, $code);
    }


    protected function getEndpoint(): string
    {
        return match (config('autotrader.environment')) {
            'production' => AutoTraderEndpoints::ProductionUrl->value,
            default => AutoTraderEndpoints::SandboxUrl->value,
        };
    }
}
