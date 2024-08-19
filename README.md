# NorthBees Auto Trader API

A Laravel wrapper for the Auto Trader API [https://developers.autotrader.co.uk/api]

## Authentication

Add the following values to your .env

-   `AUTOTRADER_ENVIRONMENT=` either `sandbox` or `production`
-   `AUTOTRADER_KEY=""` as provided
-   `AUTOTRADER_SECRET=` as provided

Authentication is done automatically when any other call is made. The token will be cached.

## Usage

The package is a simple lightweight wrapper around the Auto Trader API.

### Vehicle Request

```
// Basic Request
$vehicle = app(AutoTraderApi::class)->getVehicle($advertiserId, $vrm);

// Request with mileage
$vehicle = app(AutoTraderApi::class)->getVehicle($advertiserId, $vrm, $mileage);

// Request with mileage and additional data
$vehicle = app(AutoTraderApi::class)->getVehicle($advertiserId, $vrm, $mileage, [
    'features' => true,
    'motTests' => false,
    'history' => false,
    'fullVehicleCheck' => false,
    'valuations' => false,
    'vehicleMetrics' => false,
]);
```

### Valuation Request

```
// To request a valuation, first complete a vehicle request
$vehicle = app(AutoTraderApi::class)->getVehicle($advertiserId, $vrm);

// Then do a valuation lookup
$valuation = app(AutoTraderApi::class)->getValuation($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);

//You can also pass additional data to adjust your valuation
$valuation = app(AutoTraderApi::class)->getValuation($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate, [
        'totalPrice' => null,
        'features' => null,
        'conditionRating' => null,
    ]));
```

### Future and Historic Valuation Requests

```
$historic = app(AutoTraderApi::class)->getHistoricValuation($advertiserId, $vehicle->derivativeId, $historicOdometerReadingMiles, $firstRegistrationDate,  $historicValuationDate);
$future = app(AutoTraderApi::class)->getFutureValuation($advertiserId, $vehicle->derivativeId, $futureOdometerReadingMiles, $firstRegistrationDate,  $futureValuationDate);

```

### Taxonomy Requests

```
$taxonomy = app(AutoTraderApi::class)->getVehicleTypes($advertiserId);

/// Production status is optional, and can be Current, Discontinued or Future
$taxonomy = app(AutoTraderApi::class)->getMakes($advertiserId, $vehicleType, $productionStatus);
$taxonomy = app(AutoTraderApi::class)->getModels($advertiserId, $makeId, $model, $productionStatus);
$taxonomy = app(AutoTraderApi::class)->getGenerations($advertiserId, $modelId, $productionStatus);
$taxonomy = app(AutoTraderApi::class)->getDerivatives($advertiserId, $generationId, $productionStatus);

//Effective date is optional
$taxonomy = app(AutoTraderApi::class)->getFeatures($advertiserId, $derivativeId, $effectiveDate, $productionStatus);
$taxonomy = app(AutoTraderApi::class)->getPrices($advertiserId, $derivativeId, $effectiveDate, $productionStatus);
$taxonomy = app(AutoTraderApi::class)->getTechnicalData($advertiserId, $derivativeId);

// Facets are: fuelTypes, transmissionTypes, bodyTypes, trims, doors, drivetrains, wheelbaseTypes, cabTypes, axleConfigurations, badgeEngineSizes, styles, subStyles, endLayouts, bedroomLayouts
$taxonomy = app(AutoTraderApi::class)->getFacets( $advertiserId,  $facet,  $generationId,  $productionStatus);
```

### Image Upload

```
$imageId = app(AutoTraderApi::class)->addImage($advertiserId, $filePath);
```

### Vehicle Metrics

```
$valuation = app(AutoTraderApi::class)->getMetrics($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);
```
