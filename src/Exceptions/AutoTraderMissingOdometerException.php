<?php

namespace NorthBees\AutoTraderApi\Exceptions;

use Exception;

class AutoTraderMissingOdometerException extends Exception
{
    public $message = 'The odometer value is required for valuation and metric lookups';

    public $code = 404;
}
