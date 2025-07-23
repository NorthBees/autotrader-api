# Project Overview - NorthBees Autotrader API

## Overview

This is a Laravel package that provides a comprehensive wrapper for the Autotrader API (https://developers.autotrader.co.uk/api). The package simplifies integration with Autotrader's vehicle data, valuation, taxonomy, and finance services for PHP applications, particularly Laravel projects.

## Architecture

### Core Components

**Main API Class (`AutotraderApi.php`)**
- Central orchestrator for all API interactions
- Handles authentication token management and caching
- Implements all major API endpoints through trait composition

**Service Provider (`AutotraderApiServiceProvider.php`)**
- Laravel service provider for package registration
- Handles configuration binding and dependency injection

**Trait-Based Architecture**
The package uses a modular trait-based architecture for different API functionalities:

- `AutotraderAuthenticationTrait` - Handles OAuth authentication and token management
- `AutotraderVehiclesTrait` - Vehicle lookup and search operations
- `AutotraderValuationsTrait` - Current vehicle valuations
- `AutotraderHistoricValuationsTrait` - Historical valuation data
- `AutotraderFutureValuationsTrait` - Future valuation projections
- `AutotraderTaxonomyTrait` - Vehicle taxonomy (makes, models, derivatives, etc.)
- `AutotraderFinanceTrait` - Finance options and applications
- `AutotraderImageTrait` - Image upload functionality
- `AutotraderStockTrait` - Dealer stock management
- `AutotraderVehicleMetricsTrait` - Vehicle performance metrics

**Exception Handling**
Comprehensive exception hierarchy for different error scenarios:
- `AutotraderException` - Base exception class
- `AutotraderFailedConnectionException` - Network/connectivity issues
- `AutotraderClientErrorException` - Client-side errors (4xx responses)
- `AutotraderMissingOdometerException` - Missing odometer data
- `AutotraderNoAdvertiserIdException` - Missing advertiser ID
- `AutotraderWarning` - Non-fatal warnings

**Validation**
- `AbstractAutotraderValidator` - Base validation class
- `AutotraderVehicleMetricsOptionsValidator` - Validates vehicle metrics request options

**Enums**
- `AutotraderEndpoints` - API endpoint definitions for different environments

### Data Flow

1. **Authentication**: Automatic token retrieval and caching on first API call
2. **Request Processing**: API calls routed through appropriate traits
3. **Response Handling**: Structured response parsing with error handling
4. **Caching**: Token caching to avoid unnecessary authentication requests

### Environment Support

- **Sandbox Environment**: For development and testing
- **Production Environment**: For live API interactions
- Environment selection via `AUTOTRADER_ENVIRONMENT` configuration

## Development Standards

### PHP Version
- **Required PHP Version**: 8.4
- The codebase should maintain compatibility with PHP 8.4 features and syntax

### Testing Framework
- **Primary Testing Framework**: Pest PHP
- All tests must be written using Pest PHP syntax and conventions
- Test organization:
  - `tests/Feature/` - Integration and feature tests
  - `tests/Unit/` - Unit tests for individual components
  - `tests/Traits/` - Trait-specific tests

### Code Style
- Follow existing code style patterns throughout the codebase
- Maintain consistency with current naming conventions and structure
- Use PSR-4 autoloading standards
- Implement proper type hints and return types (PHP 8.4 compatible)

### UX/Developer Experience
- Maintain fluent, Laravel-style API interfaces
- Preserve existing method signatures for backward compatibility
- Ensure clear, descriptive error messages
- Follow Laravel package development best practices

## API Capabilities

### Vehicle Operations
- Single vehicle lookup by VRM (Vehicle Registration Mark)
- Vehicle search with multiple criteria
- Vehicle features and specifications
- MOT test history
- Vehicle history reports
- Full vehicle checks

### Valuation Services
- Current market valuations
- Historical valuation data
- Future valuation projections
- Condition-adjusted valuations
- Price indicator ratings

### Taxonomy Services
- Vehicle types, makes, and models
- Generations and derivatives
- Features and factory codes
- Technical specifications
- Pricing data
- Various vehicle facets (fuel types, transmissions, etc.)

### Finance Services
- Finance option calculations
- Finance application submission
- Application updates and management

### Stock Management
- Dealer stock listings
- Stock filtering and search
- Enhanced stock data with pricing indicators

### Image Management
- Image upload capabilities for vehicle listings

## Configuration

The package requires the following environment variables:
- `AUTOTRADER_ENVIRONMENT` - Either 'sandbox' or 'production'
- `AUTOTRADER_KEY` - API key provided by Autotrader
- `AUTOTRADER_SECRET` - API secret provided by Autotrader

## Dependencies

### Runtime Dependencies
- `illuminate/support` ^11.0|^12.0 - Laravel framework support

### Development Dependencies
- `phpunit/phpunit` ~9.0 - Testing framework base
- `orchestra/testbench` ~7 - Laravel package testing utilities
- `roave/security-advisories` dev-latest - Security vulnerability monitoring

## Package Structure

```
src/
├── AutotraderApi.php              # Main API class
├── AutotraderApiServiceProvider.php # Laravel service provider
├── Enum/
│   └── AutotraderEndpoints.php    # API endpoint definitions
├── Exceptions/                     # Exception classes
├── Traits/                         # Modular API functionality
└── Validators/                     # Request validation

tests/
├── Feature/                        # Integration tests
├── Unit/                          # Unit tests
└── Traits/                        # Trait-specific tests

config/                            # Configuration files
routes/                           # Package routes (if any)
```

## Usage Patterns

The package follows Laravel's service container patterns:
```php
// Typical usage
$api = app(AutotraderApi::class);
$vehicle = $api->getVehicle($advertiserId, $vrm);
$valuation = $api->getValuation($advertiserId, $vehicle->derivativeId, $mileage, $vehicle->firstRegistrationDate);
```

## Testing Strategy

- **Feature Tests**: Test complete workflows and API integrations
- **Unit Tests**: Test individual components and validators
- **HTTP Mocking**: Use Laravel's HTTP fake for testing API interactions
- **Trait Testing**: Specific tests for trait functionality

This architecture provides a clean, maintainable, and extensible foundation for Autotrader API integration while following Laravel best practices and maintaining high code quality standards.