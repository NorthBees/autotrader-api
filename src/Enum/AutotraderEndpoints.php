<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

enum AutotraderEndpoints: string
{
    case SandboxUrl = 'https://api-sandbox.autotrader.co.uk';
    case ProductionUrl = 'http://api-sandbox.autotrader.co.uk';
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
    case Delivery = 'delivery';
    case Calls = 'calls';

}
