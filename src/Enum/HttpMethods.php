<?php

namespace NorthBees\AutoTraderApi\Enum;

enum HttpMethods: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
}
