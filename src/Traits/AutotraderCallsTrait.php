<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderCallsTrait
{
    /**
     * Get call details by call ID
     *
     * @param int $advertiserId
     * @param string $callId
     * @return array
     */
    public function getCalls(int $advertiserId, string $callId): array
    {
        $url = AutotraderEndpoints::Calls->value . '/' . $callId . '?advertiserId=' . $advertiserId;

        return $this->performRequest(HttpMethods::GET, $url, [], []);
    }
}