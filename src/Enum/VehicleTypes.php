<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Enum;

enum VehicleTypes: string
{
    case Bike = 'Bike';
    case Car = 'Car';
    case Caravan = 'Caravan';
    case Crossover = 'Crossover';
    case Farm = 'Farm';
    case Motorhome = 'Motorhome';
    case Plant = 'Plant';
    case Truck = 'Truck';
    case Van = 'Van';
}
