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
    'factoryCodes' => true,
]);
```

### Search Request

```
// Basic search
$results = app(AutotraderApi::class)->searchVehicles($advertiserId);

// Search with criteria
$results = app(AutotraderApi::class)->searchVehicles($advertiserId, [
    'make' => 'BMW',
    'model' => 'X5',
    'priceFrom' => 10000,
    'priceTo' => 50000,
    'factoryCodes' => ['FC123', 'FC456'],
    'wheelbaseMM' => 2975,
]);

// Search with additional options
$results = app(AutotraderApi::class)->searchVehicles($advertiserId, $searchCriteria, [
    'features' => true,
    'factoryCodes' => true,
    'wheelbaseMM' => true,
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
        'priceIndicatorRatingBands' => true,
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
$taxonomy = app(AutotraderApi::class)->getFeatures($advertiserId, $derivativeId, $effectiveDate, $productionStatus, [
    'factoryCodes' => true,
]);
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

### Finance Requests

```
// Get finance options
$financeOptions = app(AutotraderApi::class)->getFinanceOptions($advertiserId, $vehicleData);

// Submit finance application (note: only months fields are used, not years)
$application = app(AutotraderApi::class)->submitFinanceApplication($advertiserId, [
    'monthsAtBank' => 40, // Previously would be yearsAtBank: 3, monthsAtBank: 4
    'monthsAtEmployer' => 36,
    'monthsAtAddress' => 48,
    // ... other finance data
]);

// Update finance application
$updated = app(AutotraderApi::class)->updateFinanceApplication($advertiserId, $applicationId, $financeData);
```

### Stock Requests

```
// Get stock list with new features
$stock = app(AutotraderApi::class)->getStockList($advertiserId, $filters, [
    'factoryCodes' => true,
    'priceIndicatorRatingBands' => true,
    'wheelbaseMM' => true,
]);
```

### Deals Requests

```
// Get all deals for an advertiser
$deals = app(AutotraderApi::class)->getDeals($advertiserId);

// Get deals with filters
$deals = app(AutotraderApi::class)->getDeals($advertiserId, [
    'page' => 1,
    'from' => '2023-05-05',
    'to' => '2023-05-07',
]);

// Get a specific deal
$deal = app(AutotraderApi::class)->getDeal($advertiserId, $dealId);

// Complete a deal
$response = app(AutotraderApi::class)->completeDeal($advertiserId, $dealId);

// Cancel a deal with reason
$response = app(AutotraderApi::class)->cancelDeal($advertiserId, $dealId, 'Unaffordable', 'Customer cannot afford the deposit');

// Update a deal with custom data
$response = app(AutotraderApi::class)->updateDeal($advertiserId, $dealId, [
    'advertiserDealStatus' => 'Complete'
]);

// Remove deal components
$response = app(AutotraderApi::class)->removeDealPartExchange($advertiserId, $dealId);
$response = app(AutotraderApi::class)->removeDealFinanceApplication($advertiserId, $dealId);
```
### Messages Requests

```
// Get messages for a specific message ID
$messages = app(AutotraderApi::class)->getMessages($advertiserId, $messagesId);

// Mark messages as read
$response = app(AutotraderApi::class)->markMessagesAsRead($advertiserId, $messagesId);

// Send a new message for a new conversation
$response = app(AutotraderApi::class)->sendMessage($advertiserId, [
    'dealId' => '1a0e00aa-459b-162d-a23a-adcbb1110f04',
    'message' => 'Your message here (max 1500 characters)'
]);

// Send a message to an existing conversation
$response = app(AutotraderApi::class)->sendMessage($advertiserId, [
    'messagesId' => 'e00a1a0a-162d-459b-a23a-0f04adcbb111',
    'message' => 'Your reply message here'
]);
### Delivery Requests

```php
// Get delivery details
$delivery = app(AutotraderApi::class)->getDelivery($advertiserId, $deliveryId);
// Get call details
$calls = app(AutotraderApi::class)->getCalls($advertiserId, $callId);
```
