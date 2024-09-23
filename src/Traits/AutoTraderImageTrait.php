<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Traits;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use NorthBees\AutoTraderApi\Enum\AutoTraderEndpoints;
use NorthBees\AutoTraderApi\Exceptions\AutoTraderException;

trait AutoTraderImageTrait
{
    public function addImage(int $advertiserId, $file)
    {

        $url = implode('/', [$this->getEndpoint(), AutoTraderEndpoints::Images->value . '?advertiserId=' . $advertiserId]);

        $response = Http::withToken($this->getAuthenticationCode())->attach(
            'file',
            Utils::tryFopen($file, 'r'),
        )->post($url);
        if ($response->successful()) {
            return $response->json();
        }

        throw new AutoTraderException($response->json('message'), $response->json('code'));
    }
}
