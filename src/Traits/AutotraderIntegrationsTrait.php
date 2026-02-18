<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;

trait AutotraderIntegrationsTrait
{
    /**
     * Get integrations for a given partner
     *
     * Returns a view of what integrations a given partner has access to.
     * Integrations can be API, Datafeeds, or Exports.
     * If the integration is an API, the response will show all the capabilities
     * the integration has access to, including which APIs and methods can be accessed.
     *
     * @param  array  $options  Optional query parameters
     * @return array
     */
    public function getIntegrations(array $options = [])
    {
        return $this->performRequestWithoutAdvertiserId(
            HttpMethods::GET,
            AutotraderEndpoints::Integrations->value,
            [],
            $options,
        );
    }
}
