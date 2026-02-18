<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

enum AutotraderEndpoints: string
{
    case SandboxUrl = 'https://api-sandbox.autotrader.co.uk';
    case ProductionUrl = 'https://api.autotrader.co.uk';
    case Authenticate = 'authenticate';
    case Vehicles = 'vehicles';
    case Taxonomy = 'taxonomy';
    case Stock = 'stock';
    case Images = 'images';
    case Search = 'search';
    case Valuations = 'valuations';
    case FutureValuations = 'future-valuations';
    case HistoricValuations = 'historic-valuations';
    case VehicleMetrics = 'vehicle-metrics';
    case Advertisers = 'advertisers';
    case CoDriver = 'co-driver/stock';
    case Finance = 'finance';
    case Deals = 'deals';
    case Messages = 'messages';
    case Delivery = 'delivery';
    case Calls = 'calls';
    case Integrations = 'integrations';

}
