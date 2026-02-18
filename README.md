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

// Search with monthly price option (replaces deprecated financeOption)
$results = app(AutotraderApi::class)->searchVehicles($advertiserId, [
    'make' => 'BMW',
    'monthlyPriceOption' => [
        'mileage' => 10000,
        'deposit' => 1000,
        'term' => 48,
    ],
]);

// Search with additional options including financeOffers (headlineOffer)
$results = app(AutotraderApi::class)->searchVehicles($advertiserId, $searchCriteria, [
    'features' => true,
    'factoryCodes' => true,
    'wheelbaseMM' => true,
    'financeOffers' => true, // Includes headlineOffer in response
]);
```

**Search API response notes:**
- `financeOffers.headlineOffer`: Available when `financeOffers` option is enabled (Aug 2025)
- `vehicle.origin`: Indicates if the vehicle is UK or Non UK specification (Oct 2025)
- `rarityRating`, `valueRating`: Autotrader intelligence ratings for vehicle features (Aug 2025)
- `financeOption` search parameter is **deprecated** - use `monthlyPriceOption` instead (Feb 2026)

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

// Search by OEM model code (e.g. Volvo derivatives) - requires generationId
$taxonomy = app(AutotraderApi::class)->getDerivatives($advertiserId, $generationId, null, $oemModelCode);

//Effective date is optional
$taxonomy = app(AutotraderApi::class)->getFeatures($advertiserId, $derivativeId, $effectiveDate, $productionStatus, [
    'factoryCodes' => true,
]);
$taxonomy = app(AutotraderApi::class)->getPrices($advertiserId, $derivativeId, $effectiveDate, $productionStatus);
$taxonomy = app(AutotraderApi::class)->getTechnicalData($advertiserId, $derivativeId);

// Facets are: fuelTypes, transmissionTypes, bodyTypes, trims, doors, drivetrains, wheelbaseTypes, cabTypes, axleConfigurations, badgeEngineSizes, styles, subStyles, endLayouts, bedroomLayouts
$taxonomy = app(AutotraderApi::class)->getFacets( $advertiserId,  $facet,  $generationId,  $productionStatus);
```

**Taxonomy API response notes:**
- `rarityRating`, `valueRating`: Autotrader intelligence ratings for vehicle features (Aug 2025)
- Manufacturer warranty details (paintwork, standard, corrosion, battery) available in Taxonomy and Vehicles APIs (Oct 2025)

### Image Upload

```
$imageId = app(AutotraderApi::class)->addImage($advertiserId, $filePath);
```

### Vehicle Metrics

```
$valuation = app(AutotraderApi::class)->getMetrics($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);

// With vatStatus for commercial vehicle No VAT valuations
$valuation = app(AutotraderApi::class)->getVehicleMetrics($advertiserId, $derivativeId, $mileage, $firstRegistrationDate, [
    'vatStatus' => 'NO_VAT',
]);
```

### Finance Requests

```
// Get finance options (quotes)
$financeOptions = app(AutotraderApi::class)->getFinanceOptions($advertiserId, $vehicleData);

// Submit finance application (note: only months fields are used, not years)
// Use financeTerms.productType instead of deprecated financeTerms.product
// Use applicant.replacingExistingLoan instead of deprecated affordability.replacingExistingLoan
// Use applicant.lastName instead of deprecated applicant.surname
$application = app(AutotraderApi::class)->submitFinanceApplication($advertiserId, [
    'monthsAtBank' => 40, // Previously would be yearsAtBank: 3, monthsAtBank: 4
    'monthsAtEmployer' => 36,
    'monthsAtAddress' => 48,
    'financeTerms' => [
        'productType' => 'PCP', // Use productType, not deprecated product
    ],
    'applicant' => [
        'lastName' => 'Smith', // Use lastName, not deprecated surname
        'replacingExistingLoan' => true, // Use this, not deprecated affordability.replacingExistingLoan
        'monthlyRentOrMortgage' => ['amountGBP' => 800], // Use this, not deprecated monthlyRentOrMortgageGBP
        'monthlyChildcare' => ['amountGBP' => 200], // Use this, not deprecated monthlyChildCareGBP
    ],
    // ... other finance data
]);

// Update finance application
$updated = app(AutotraderApi::class)->updateFinanceApplication($advertiserId, $applicationId, $financeData);
```

**Finance API deprecation notes (Feb 2026):**
- `financeTerms.product` is deprecated - use `financeTerms.productType`
- `product` in quotes/proposals is deprecated - use `productType`
- `productName` added to quotes for lender specific product name
- `affordability.replacingExistingLoan` is deprecated - use `applicant.replacingExistingLoan`

**Finance API deprecation notes (Oct/Nov 2025):**
- `applicant.surname` is deprecated and removed - use `applicant.lastName`
- `applicant.monthlyRentOrMortgageGBP.amountGBP` is deprecated and removed - use `applicant.monthlyRentOrMortgage.amountGBP`
- `applicant.monthlyChildCareGBP.amountGBP` is deprecated and removed - use `applicant.monthlyChildcare.amountGBP`
- `questions` in quotes is deprecated and removed - use `quotesRequirements`
- `ineligibilityReasons` in quotes is deprecated and removed - use `quotesRequirements`
- `proposalRequirements` and `quotesRequirements` added to Quotes response

### Stock Requests

```
// Get stock list with new features
$stock = app(AutotraderApi::class)->getStockList($advertiserId, $filters, [
    'factoryCodes' => true,
    'priceIndicatorRatingBands' => true,
    'wheelbaseMM' => true,
]);

// Get real-time stock summary
$summary = app(AutotraderApi::class)->getStockSummary($advertiserId, $stockId);

// Update stock - mark as SOLD and unpublish tradeAdvert
$updated = app(AutotraderApi::class)->updateStock($advertiserId, [
    'metadata' => ['stockId' => $stockId, 'lifecycleState' => 'SOLD'],
    'adverts' => ['tradeAdvert' => ['status' => 'NOT_PUBLISHED']],
]);
```

**Stock API response notes:**
- `eligibleContractAllowances`, `allocatedContractAllowance`: Contract allowance information (Aug 2025)
- `vehicle.origin`: Indicates if the vehicle is UK or Non UK specification (Oct 2025)
- tradeAdvert can now be set to NOT_PUBLISHED when updating stock lifecycle to SOLD (Feb 2026)

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

// Create a new deal (originated outside of Autotrader consumer journey)
$deal = app(AutotraderApi::class)->createDeal($advertiserId, [
    'stockId' => $stockId,
    // ... deal data
]);

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

**Deals API response notes:**
- `buyingSignals`: Consumer behaviour indicators including dealIntentScore, intent, localCustomer, advertSaved, preferences (Nov 2025)
- `reservation` object: Replaces deprecated `stock.reservationStatus` and `consumerReservationFeeStatus`. Includes status values Requested and Reserved (Jan 2026)
- `stock.reservationStatus` is **deprecated** - use `reservation` object instead (Jan 2026)
- `consumerReservationFeeStatus` is **deprecated** - use `reservation` object instead (Jan 2026)
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
```
### Delivery Requests

```php
// Get delivery details
$delivery = app(AutotraderApi::class)->getDelivery($advertiserId, $deliveryId);
// Get call details
$calls = app(AutotraderApi::class)->getCalls($advertiserId, $callId);
```

### Integrations Requests

```php
// Get integrations for the current partner
$integrations = app(AutotraderApi::class)->getIntegrations();
```

**Integrations API notes:**
- Returns a view of what integrations a partner has access to (API, Datafeeds, Exports)
- For API integrations, shows all capabilities the integration has access to
- Introduced Nov 2025

### Advertisers Requests

```php
// Get advertisers
$advertisers = app(AutotraderApi::class)->getAdvertisers();
```

**Advertisers API response notes:**
- `capabilities` object: Lists atConnect capabilities a partner's application is permitted to use on behalf of an advertiser (Oct 2025)

### Valuation API Response Notes

- `amountNoVatGBP` fields for retail, trade, and part exchange valuations are available for vehicles not requiring VAT (Aug 2025)
- Also available in Historic Valuations and Vehicles APIs

### Vehicle API Response Notes

- `rarityRating`, `valueRating`: Autotrader intelligence ratings for vehicle features (Aug 2025)
- Manufacturer warranty details (paintwork, standard, corrosion, battery) provided by manufacturer for brand new vehicles (Oct 2025)
