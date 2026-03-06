# NorthBees Autotrader API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/northbees/autotrader-api.svg?style=flat-square)](https://packagist.org/packages/northbees/autotrader-api)
[![Tests](https://github.com/northbees/autotrader-api/actions/workflows/tests.yml/badge.svg)](https://github.com/northbees/autotrader-api/actions/workflows/tests.yml)
[![License](https://img.shields.io/packagist/l/northbees/autotrader-api.svg?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/northbees/autotrader-api.svg?style=flat-square)](composer.json)

A Laravel SDK for the [Autotrader API](https://developers.autotrader.co.uk/api) - vehicles, valuations, stock management, finance, deals, and more.

## Requirements

- PHP 8.4+
- Laravel 11 or 12

## Installation

```bash
composer require northbees/autotrader-api
```

The package uses Laravel's auto-discovery, so the service provider will be registered automatically.

Publish the configuration file:

```bash
php artisan vendor:publish --tag=autotrader.config
```

## Configuration

Add the following to your `.env` file:

```env
AUTOTRADER_ENVIRONMENT=sandbox
AUTOTRADER_KEY=your-api-key
AUTOTRADER_SECRET=your-api-secret
```

See [.env.example](.env.example) for all available options.

Authentication is handled automatically when any API call is made. The token will be cached.

## Usage

The package is a lightweight wrapper around the Autotrader API.

### Vehicle Request

```php
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

```php
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

```php
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

```php
$historic = app(AutotraderApi::class)->getHistoricValuation($advertiserId, $vehicle->derivativeId, $historicOdometerReadingMiles, $firstRegistrationDate,  $historicValuationDate);
$future = app(AutotraderApi::class)->getFutureValuation($advertiserId, $vehicle->derivativeId, $futureOdometerReadingMiles, $firstRegistrationDate,  $futureValuationDate);
```

**Future and Historic Valuation API response notes:**
- `amountNoVatGBP` fields for retail, trade, and partExchange valuations are now available in Trended Valuations and Future Valuations APIs (Mar 2026) - LCVs only, produced alongside `amountExVatGBP`

### Taxonomy Requests

```php
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

```php
$imageId = app(AutotraderApi::class)->addImage($advertiserId, $filePath);
```

### Vehicle Metrics

```php
$valuation = app(AutotraderApi::class)->getMetrics($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);

// With vatStatus for commercial vehicle No VAT valuations
$valuation = app(AutotraderApi::class)->getVehicleMetrics($advertiserId, $derivativeId, $mileage, $firstRegistrationDate, [
    'vatStatus' => 'NO_VAT',
]);
```

### Finance Requests

```php
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

// Get a finance application by ID
// Note: anonymised applications return {applicationId, status: "Expired"} instead of a 451 error
$application = app(AutotraderApi::class)->getFinanceApplication($advertiserId, $applicationId);
if (($application['status'] ?? null) === 'Expired') {
    // Application has been anonymised due to legal reasons
}
```

**Finance API field removals (Mar 2026):**
- `financeTerms.product` has been **removed** - use `financeTerms.productType`
- `product` in quotes/proposals has been **removed** - use `productType`
- `affordability.replacingExistingLoan` has been **removed** - use `applicant.replacingExistingLoan`
- `affordability.affordableLoan` has been **removed**
- `productName` has been added to quotes for lender specific product name
- Anonymised finance applications now return HTTP 200 with `{applicationId, status: "Expired"}` instead of HTTP 451

**Finance API field removals (Oct/Nov 2025):**
- `applicant.surname` has been removed - use `applicant.lastName`
- `applicant.monthlyRentOrMortgageGBP.amountGBP` has been removed - use `applicant.monthlyRentOrMortgage.amountGBP`
- `applicant.monthlyChildCareGBP.amountGBP` has been removed - use `applicant.monthlyChildcare.amountGBP`
- `questions` in quotes has been removed - use `quotesRequirements`
- `ineligibilityReasons` in quotes has been removed - use `quotesRequirements`
- `proposalRequirements` and `quotesRequirements` added to Quotes response

### Stock Requests

```php
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

```php
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
  - `preferences` now includes `wheelbaseTypes` (Mar 2026) - only available for Van consumer activity
- `reservation` object: Replaces deprecated `stock.reservationStatus` and `consumerReservationFeeStatus`. Includes status values Requested and Reserved (Jan 2026)
- `stock.reservationStatus` is **deprecated** - use `reservation` object instead (Jan 2026)
- `consumerReservationFeeStatus` is **deprecated** - use `reservation` object instead (Jan 2026)

### Messages Requests

```php
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

## Testing

```bash
composer test
```

## Code Style

This package uses [Laravel Pint](https://laravel.com/docs/pint) for code style:

```bash
composer lint
```

## Static Analysis

[PHPStan](https://phpstan.org/) with [Larastan](https://github.com/larastan/larastan) is used for static analysis:

```bash
composer analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](SECURITY.md) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
