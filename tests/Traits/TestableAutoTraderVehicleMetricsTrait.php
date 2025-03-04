<?php

namespace NorthBees\AutoTraderApi\Tests\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use NorthBees\AutoTraderApi\Traits\AutoTraderVehicleMetricsTrait;

/**
 * TestableAutoTraderVehicleMetricsTrait
 * that exposes the protected methods of the AutoTraderVehicleMetricsTrait
 */
class TestableAutoTraderVehicleMetricsTrait
{
    use AutoTraderVehicleMetricsTrait;

    /**
     * @param array $options
     * @return array
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    public function publicFormatVehicleMetricOptions(array $options): array
    {
        return $this->formatVehicleMetricOptions($options);
    }
}
