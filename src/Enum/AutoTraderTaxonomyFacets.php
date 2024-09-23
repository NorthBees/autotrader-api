<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Enum;

enum AutoTraderTaxonomyFacets: string
{
    case fuelTypes = 'fuelTypes';
    case transmissionTypes = 'transmissionTypes';
    case bodyTypes = 'bodyTypes';
    case trims = 'trims';
    case doors = 'doors';
    case drivetrains = 'drivetrains';
    case wheelbaseTypes = 'wheelbaseTypes';
    case cabTypes = 'cabTypes';
    case axleConfigurations = 'axleConfigurations';
    case badgeEngineSizes = 'badgeEngineSizes';
    case styles = 'styles';
    case subStyles = 'subStyles';
    case endLayouts = 'endLayouts';
    case bedroomLayouts = 'bedroomLayouts';
}
