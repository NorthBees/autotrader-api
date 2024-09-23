<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Enum;

enum AutoTraderTaxonomies: string
{
    case VehicleTypes = 'vehicleTypes';
    case Makes = 'makes';
    case Models = 'models';
    case Generations = 'generations';
    case Derivatives = 'derivatives';
    case Features = 'features';
    case Prices = 'prices';

}
