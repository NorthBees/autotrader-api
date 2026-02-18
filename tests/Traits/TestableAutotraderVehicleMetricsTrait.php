<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Tests\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use NorthBees\AutotraderApi\Traits\AutotraderVehicleMetricsTrait;

/**
 * TestableAutotraderVehicleMetricsTrait
 * that exposes the protected methods of the AutotraderVehicleMetricsTrait.
 */
class TestableAutotraderVehicleMetricsTrait
{
    use AutotraderVehicleMetricsTrait;

    /**
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    public function publicFormatVehicleMetricOptions(array $options): array
    {
        return $this->formatVehicleMetricOptions($options);
    }
}
