# Changelog

All notable changes to `AutotraderApi` will be documented in this file.

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
