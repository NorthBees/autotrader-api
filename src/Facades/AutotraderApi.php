<?php

namespace NorthBees\AutotraderApi\Facades;

use Illuminate\Support\Facades\Facade;

class AutotraderApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'autotraderapi';
    }
}
