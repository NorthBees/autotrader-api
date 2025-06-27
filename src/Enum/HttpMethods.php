<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

enum HttpMethods: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case PATCH = 'patch';
    case DELETE = 'delete';
}
