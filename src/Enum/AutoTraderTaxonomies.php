<?php

namespace NorthBees\AutotraderApi\Enum;

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
