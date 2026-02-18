# Changelog

All notable changes to `AutotraderApi` will be documented in this file.

## Version 1.2

### Added

- Added Integrations API support via new `AutotraderIntegrationsTrait` with `getIntegrations()` method
- Added `Integrations` endpoint to `AutotraderEndpoints` enum
- Added `createDeal()` method to Deals API for creating deals originated outside Autotrader
- Added `getStockSummary()` method to Stock API for real-time stock state information
- Added `financeOffers` option to Search API for headlineOffer access
- Added `monthlyPriceOption` search parameter to Search API (replaces deprecated `financeOption`)
- Added `vatStatus` option to Vehicle Metrics API for No VAT commercial vehicle valuations
- Added `oemModelCode` parameter to `getDerivatives()` in Taxonomy API for OEM model code search (e.g. Volvo)
- Added `AutotraderTradeAdvertStates` enum with PUBLISHED and NOT_PUBLISHED states

### Changed

- Updated Stock API `updateStock()` to document NOT_PUBLISHED tradeAdvert support when marking stock as SOLD

### Deprecated

The following fields are deprecated per Autotrader API changes. Both old and new fields are supported during the transition period.

**Finance API (Feb 2026):**
- `financeTerms.product` - use `financeTerms.productType` instead
- `product` in quotes endpoint - use `productType` instead (also `productName` added for lender specific name)
- `product` in proposals endpoint - use `productType` instead
- `affordability.replacingExistingLoan` - use `applicant.replacingExistingLoan` instead

**Finance API (Oct/Nov 2025):**
- `applicant.surname` removed - use `applicant.lastName`
- `applicant.monthlyRentOrMortgageGBP.amountGBP` removed - use `applicant.monthlyRentOrMortgage.amountGBP`
- `applicant.monthlyChildCareGBP.amountGBP` removed - use `applicant.monthlyChildcare.amountGBP`
- `questions` in quotes removed - use `quotesRequirements`
- `ineligibilityReasons` in quotes removed - use `quotesRequirements`

**Deals API (Jan 2026):**
- `stock.reservationStatus` - use `reservation` object instead
- `consumerReservationFeeStatus` - use `reservation` object instead

**Search API (Feb 2026):**
- `financeOption` parameter - use `monthlyPriceOption` instead

### Response-Only Changes (no SDK code changes needed)

These are new response fields from the Autotrader API that are automatically available in API responses:

- `amountNoVatGBP` valuations fields in Vehicles, Stock, Valuations, Historic Valuations APIs (Aug 2025)
- `rarityRating`, `valueRating` in Vehicles, Taxonomy, Stock, Search APIs (Aug 2025)
- `eligibleContractAllowances`, `allocatedContractAllowance` in Stock API (Aug 2025)
- `financeOffers.headlineOffer` in Search API (Aug 2025)
- Manufacturer warranty details in Taxonomy and Vehicles APIs (Oct 2025)
- `buyingSignals` in Deals API (Oct 2025 sandbox, Nov 2025 production)
- `vehicle.origin` in Stock and Search APIs (Oct 2025)
- `capabilities` in Advertisers API (Oct 2025)
- `proposalRequirements`, `quotesRequirements` in Quotes API (Oct 2025)
- `reservation` object in Deals API (Jan 2026)
- `productType`, `productName` in Quotes response (Feb 2026)

## Version 1.1

### Added

- Added Finance API support with months-only fields (years fields removed as per API updates)
- Added Search API support with factoryCodes and wheelbaseMM fields
- Added factoryCodes support to Stock, Vehicles, and Taxonomy APIs
- Added priceIndicatorRatingBands support to Stock and Valuations APIs
- Added wheelbaseMM support to Stock and Search APIs
- Updated README documentation with new API examples

### Changed

- Finance API now uses months-only fields instead of years+months (e.g., monthsAtBank: 40 instead of yearsAtBank: 3, monthsAtBank: 4)
- Extended Stock API options to include factoryCodes, priceIndicatorRatingBands, and wheelbaseMM
- Extended Vehicles API options to include factoryCodes
- Extended Taxonomy Features API to support factoryCodes options
- Extended Valuations API to support priceIndicatorRatingBands

## Version 1.0

### Added

-   Everything
