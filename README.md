# NorthBees Autotrader API

A Laravel wrapper for the Autotrader API [https://developers.autotrader.co.uk/api]

## Authentication

Add the following values to your .env

-   `AUTOTRADER_ENVIRONMENT=` either `sandbox` or `production`
-   `AUTOTRADER_KEY=""` as provided
-   `AUTOTRADER_SECRET=` as provided

Authentication is done automatically when any other call is made. The token will be cached.

## Usage

The package is a simple lightweight wrapper around the Autotrader API.

### Vehicle Request

```
// Basic Request
$vehicle = app(AutotraderApi::class)->getVehicle($advertiserId, $vrm);

// Request with mileage
$vehicle = app(AutotraderApi::class)->getVehicle($advertiserId, $vrm, $mileage);

// Request with mileage and additional data
$vehicle = app(AutotraderApi::class)->getVehicle($advertiserId, $vrm, $mileage, [
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
$vehicle = app(AutotraderApi::class)->getVehicle($advertiserId, $vrm);

// Then do a valuation lookup
$valuation = app(AutotraderApi::class)->getValuation($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);

//You can also pass additional data to adjust your valuation
$valuation = app(AutotraderApi::class)->getValuation($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate, [
        'totalPrice' => null,
        'features' => null,
        'conditionRating' => null,
    ]));
```

### Future and Historic Valuation Requests

```
$historic = app(AutotraderApi::class)->getHistoricValuation($advertiserId, $vehicle->derivativeId, $historicOdometerReadingMiles, $firstRegistrationDate,  $historicValuationDate);
$future = app(AutotraderApi::class)->getFutureValuation($advertiserId, $vehicle->derivativeId, $futureOdometerReadingMiles, $firstRegistrationDate,  $futureValuationDate);

```

### Taxonomy Requests

```
$taxonomy = app(AutotraderApi::class)->getVehicleTypes($advertiserId);

/// Production status is optional, and can be Current, Discontinued or Future
$taxonomy = app(AutotraderApi::class)->getMakes($advertiserId, $vehicleType, $productionStatus);
$taxonomy = app(AutotraderApi::class)->getModels($advertiserId, $makeId, $model, $productionStatus);
$taxonomy = app(AutotraderApi::class)->getGenerations($advertiserId, $modelId, $productionStatus);
$taxonomy = app(AutotraderApi::class)->getDerivatives($advertiserId, $generationId, $productionStatus);

//Effective date is optional
$taxonomy = app(AutotraderApi::class)->getFeatures($advertiserId, $derivativeId, $effectiveDate, $productionStatus);
$taxonomy = app(AutotraderApi::class)->getPrices($advertiserId, $derivativeId, $effectiveDate, $productionStatus);
$taxonomy = app(AutotraderApi::class)->getTechnicalData($advertiserId, $derivativeId);

// Facets are: fuelTypes, transmissionTypes, bodyTypes, trims, doors, drivetrains, wheelbaseTypes, cabTypes, axleConfigurations, badgeEngineSizes, styles, subStyles, endLayouts, bedroomLayouts
$taxonomy = app(AutotraderApi::class)->getFacets( $advertiserId,  $facet,  $generationId,  $productionStatus);
```

### Image Upload

```
$imageId = app(AutotraderApi::class)->addImage($advertiserId, $filePath);
```

### Vehicle Metrics

```
$valuation = app(AutotraderApi::class)->getMetrics($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);
```
