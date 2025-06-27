<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Tests\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\Traits\AutotraderVehicleMetricsTrait;

/**
 * TestableAutoTraderVehicleMetricsTrait
 * that exposes the protected methods of the AutoTraderVehicleMetricsTrait.
 */
class TestableAutoTraderVehicleMetricsTrait
{
    use AutotraderVehicleMetricsTrait;

    /**
     * @param array $options
     *
     * @throws ValidationException
     * @throws BindingResolutionException
     *
     * @return array
     */
    public function publicFormatVehicleMetricOptions(array $options): array
    {
        return $this->formatVehicleMetricOptions($options);
    }
}
