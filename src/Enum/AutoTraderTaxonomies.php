<?php

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

    case TechnicalData = 'derivatives';
}
