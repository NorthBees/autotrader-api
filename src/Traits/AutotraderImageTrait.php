<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderImageTrait
{
    public function addImage(int $advertiserId, $file)
    {

        $url = implode('/', [$this->getEndpoint(), AutotraderEndpoints::Images->value . '?advertiserId=' . $advertiserId]);

        $response = Http::withToken($this->getAuthenticationCode())->attach(
            'file',
            Utils::tryFopen($file, 'r'),
        )->post($url);
        if ($response->successful()) {
            return $response->json();
        }

        throw new AutotraderException($response->json('warnings'), $response->json('code'));
    }
}
