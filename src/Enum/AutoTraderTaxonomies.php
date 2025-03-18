<?php

declare(strict_types=1);

namespace NorthBees\AutoTraderApi\Enum;

enum AutoTraderTaxonomies: string
{

    case VEHICLETYPES = 'vehicleTypes';
    case MAKES = 'makes';
    case MODELS = 'models';
    case GENERATIONS = 'generations';
    case DERIVATIVES = 'derivatives';
    case FEATURES = 'features';
    case PRICES = 'prices';

    case FUELTYPES = 'fuelTypes';
    case TRANSMISSIONTYPES = 'transmissionTypes';
    case BODYTYPES = 'bodyTypes';
    case TRIMS = 'trims';
    case DOORS = 'doors';
    case DRIVETRAINS = 'drivetrains';
    case WHEELBASETYPES = 'wheelbaseTypes';
    case CABTYPES = 'cabTypes';
    case AXLECONFIGURATIONS = 'axleConfigurations';
    case BADGEENGINESIZES = 'badgeEngineSizes';
    case STYLES = 'styles';
    case SUBSTYLES = 'subStyles';
    case ENDLAYOUTS = 'endLayouts';
    case BEDROOMLAYOUTS = 'bedroomLayouts';

}
