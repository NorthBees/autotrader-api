# Changelog

All notable changes to `AutotraderApi` will be documented in this file.

## Version 1.2.0

### Breaking Changes (Autotrader API — 28 Oct 2026)

- **`vehicle.previousOwners` removed** from the Stock API, Search API and Stock Notifications, on both the read and write paths. Read `history.previousOwners` instead. When completing a POST or PATCH supply only `vehicle.owners`, and ensure the value is inclusive of the current owner as per the field definition. Already removed from sandbox; removed from production on 28 Oct 2026. No SDK code change is required — `createStock()` and `updateStock()` pass request bodies through verbatim — but callers must update their own field mappings. See [Stock API](https://developers.autotrader.co.uk/api#stock-api)
- **New payload structure for the Vehicles API**: the response root changes from `vehicle` to `results.vehicle`, so that all Autotrader Connect APIs share a consistent structure. Both roots are served today; the `vehicle` root is removed on 28 Oct 2026. `getVehicle()` now normalises either shape back to the historic flat shape, so existing callers need no changes. See [Vehicles API](https://developers.autotrader.co.uk/api#vehicles-api)
- **Warnings error handling in the Vehicles API**: warnings are now listed at service level (root `warnings`) or at record level (`results.warnings`). Record-based warnings are currently duplicated at the root and are removed from it on 28 Oct 2026; service-level warnings are unaffected. `getVehicle()` merges both into the flat `warnings` array without duplication. See [Vehicles API](https://developers.autotrader.co.uk/api#vehicles-api)

### Changed

- `getVehicle()` normalises the Vehicles API response through `NorthBees\AutotraderApi\Support\VehicleResponseNormaliser`. The whole of `results[0]` is merged into the response root, so `vehicle`, `features`, `motTests`, `valuations`, `check`, `history` and `chargeTimes` stay where callers already expect them. Three keys are added: `totalResults` (`0` when no vehicle matched, in which case there is no `vehicle` key), plus `serviceWarnings` and `recordWarnings` for callers that need the distinction. Search and Stock responses are deliberately left alone — they return genuinely multi-record `results` arrays.

### Fixed

- `handleUnsuccessfulResponse()` no longer fatals when a `warnings` entry is a plain string or omits its `message` key, and now also collects `results[].warnings`. Repeated messages are de-duplicated, and an empty `warnings` array produces `'An unknown warning occurred'` rather than an empty message. An empty array still throws `AutotraderWarning` rather than `AutotraderException`, so callers that catch the former to swallow soft failures are unaffected.

## Version 1.1.7

### Response-Only Changes (no SDK code changes needed)

These are new response fields from the Autotrader API that are automatically available in API responses:

- `advertiserVehicleHighlight1`, `advertiserVehicleHighlight2`, `advertiserVehicleHighlight3` and `priceCommentary` added to `adverts.retailAdverts` in the Stock API, Search API and Stock Notifications (26 Jun 2026) - the highlight fields surface up to 3 Seller Highlights and `priceCommentary` appears as Seller Comments on the Autotrader website. These are advertiser-settable fields, so they can also be sent through `createStock()` / `updateStock()` under `adverts.retailAdverts`. See [Stock API](https://developers.autotrader.co.uk/api#stock-api)
- `priceCommentary` and `priceCommentaryManufacturerApproved` added to the Advertisers API (26 Jun 2026) - exposes the Seller Comments templates configured at retailer (site) level. `priceCommentaryManufacturerApproved` is an array of `{ make, manufacturerApproved, priceCommentary }` entries defining the copy used based on make and manufacturer-approved status; `priceCommentary` is the default used when a vehicle is not a specified make or approved. Configuring these templates is optional, so content may not be populated. See [Advertiser API](https://developers.autotrader.co.uk/api#search-advertisers)

## Version 1.1.6

### Response-Only Changes (no SDK code changes needed)

These are new response fields from the Autotrader API that are automatically available in API responses:

- `responseMetrics` breakdown added to the Stock API response (Jun 2026) - the `yesterday` and `lastWeek` objects now expose a natural/paid split alongside the existing totals: `naturalAdvertViews` and `paidPPCAdvertViews` (which sum to `advertViews`), and `naturalSearchViews` and `paidPPCSearchViews` (which sum to `searchViews`). Enable by passing `responseMetrics => 'true'` to `getStockList()`. See [Response Metrics](https://developers.autotrader.co.uk/api#response-metrics)

## Version 1.1.5

### Added (Autotrader API — Jun 2026)

- **Integration notifications**: Added `AutotraderNotificationTypes` enum (`ADVERTISER`, `DEALS`, `STOCK`) to reference the notification keys exposed by the new `notifications` object on the Integrations API response.

### Response-Only Changes (no SDK code changes needed)

These are new response fields from the Autotrader API that are automatically available in API responses:

- `financeTerms` object added to the Quotes response (Jun 2026) - exposes the terms used to produce the quote (`productType`, `termMonths`, `estimatedAnnualMileage`, `cashPrice`, `deposit`, `partExchange`, `outstandingFinance`). Previously only available as part of the finance application process
- `notifications` object added to the Integrations API response (Jun 2026) - shows which notifications are set up against a given integration (`advertiserNotification`, `dealsNotification`, `stockNotification`), each with a `url`, `rateLimit` (nullable) and `enabled` flag

### New Request Fields (optional)

- `applicant.existingLoanMonthlyPayment.amountGBP` added to the Applications endpoint (Jun 2026) - the applicant's existing loan monthly payment. If not provided, a lender may not be able to progress the quote to proposals depending on their own criteria; where this is the case the requirement is listed under `proposalRequirements` in the quotes response

## Version 1.1.4

### Breaking Changes (Autotrader API — May 2026)

- **Deal notification type field**: The `type` field in Deal Notifications now returns `DEAL_CREATE` or `DEAL_UPDATE` instead of `DEAL`. Added new `AutotraderDealNotificationTypes` enum with `DEAL_CREATE` and `DEAL_UPDATE` cases.
- **Removed deprecated fields**: `stock.reservationStatus` and `consumerReservationFeeStatus` have been removed from the Deals API response and Deal Notifications. Use the `reservation` object instead (available since Jan 2026).

## Version 1.1.0

### Added

- Added `getFinanceApplication()` method to Finance API for retrieving finance applications by ID
- Added PHPDoc documentation for `amountNoVatGBP` response fields in Future Valuations API
- Added PHPDoc documentation for `wheelbaseTypes` in buyingSignals preferences for Deals API

### Changed

- Finance API: `financeTerms.product`, `affordability.replacingExistingLoan`, and `affordability.affordableLoan` have been **removed** (`financeTerms.product` and `affordability.replacingExistingLoan` were previously deprecated in v1.0)
- Finance API: Anonymised finance applications now return HTTP 200 with `{applicationId, status: "Expired"}` instead of HTTP 451 error. The new `getFinanceApplication()` method documents this behaviour.

### Response-Only Changes (no SDK code changes needed)

These are new response fields from the Autotrader API that are automatically available in API responses:

- `amountNoVatGBP` valuations fields for retail, trade, and partExchange in Historic Valuations and Future Valuations APIs (Mar 2026) - LCVs only, alongside amountExVatGBP
- `wheelbaseTypes` in buyingSignals preferences in Deals API and Deals Notifications (Mar 2026) - Van consumer activity only

## Version 1.0

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
- Added Finance API support with months-only fields (years fields removed as per API updates)
- Added Search API support with factoryCodes and wheelbaseMM fields
- Added factoryCodes support to Stock, Vehicles, and Taxonomy APIs
- Added priceIndicatorRatingBands support to Stock and Valuations APIs
- Added wheelbaseMM support to Stock and Search APIs
- Updated README documentation with new API examples

### Changed

- Updated Stock API `updateStock()` to document NOT_PUBLISHED tradeAdvert support when marking stock as SOLD
- Finance API now uses months-only fields instead of years+months (e.g., monthsAtBank: 40 instead of yearsAtBank: 3, monthsAtBank: 4)
- Extended Stock API options to include factoryCodes, priceIndicatorRatingBands, and wheelbaseMM
- Extended Vehicles API options to include factoryCodes
- Extended Taxonomy Features API to support factoryCodes options
- Extended Valuations API to support priceIndicatorRatingBands

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
