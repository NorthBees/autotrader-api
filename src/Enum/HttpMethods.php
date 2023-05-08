<?php

namespace NorthBees\AutotraderApi\Enum;

enum HttpMethods: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
}
