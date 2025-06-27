<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Exceptions;

use Exception;

class AutotraderMissingOdometerException extends Exception
{
    public $message = 'The odometer value is required for valuation and metric lookups';

    public $code = 404;
}
