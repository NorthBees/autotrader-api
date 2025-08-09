# NorthBees Autotrader API Package

A Laravel PHP package providing a comprehensive wrapper for the Autotrader API (https://developers.autotrader.co.uk/api). This package integrates vehicle data, valuations, taxonomy, finance services, and deals management for Laravel applications.

**Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the info here.**

## Working Effectively

### Prerequisites and Setup
- **PHP 8.4 is REQUIRED** - The package explicitly requires PHP 8.4 in composer.json
- If PHP 8.4 is not available, you can temporarily modify composer.json to use PHP 8.3 for testing:
  - Change `"php": "^8.4"` to `"php": "^8.3"` in composer.json
  - **ALWAYS revert this change before committing**
- **Composer is required** for dependency management

### Installation and Build Process
```bash
# Check PHP version first
php --version  # Should be 8.4+, may work with 8.3

# Enable Pest plugin (required before installation)
composer config --no-plugins allow-plugins.pestphp/pest-plugin true

# Install dependencies - NEVER CANCEL, takes 60-90 seconds
timeout 600 composer install --no-interaction
```
**TIMING**: Composer installation takes approximately 62 seconds. NEVER CANCEL - set timeout to 600+ seconds to allow for network delays.

### Testing
```bash
# Run tests with Pest (primary testing framework) - NEVER CANCEL, takes 2-5 seconds
timeout 300 ./vendor/bin/pest --no-coverage

# Note: PHPUnit is NOT available - Pest is the only test runner
```
**TIMING**: Test suite completes in approximately 1.5 seconds. Tests may have some failures related to todos and mocking - this is normal for the current codebase.

### Code Quality Validation
```bash
# Check PHP syntax for all source files
find src/ -name "*.php" -exec php -l {} \;

# All 33 source files should pass syntax validation
```

## Validation Scenarios

### Basic Package Functionality
After making any changes to the codebase:
1. **Always run syntax validation** on modified PHP files
2. **Always run the test suite** with `./vendor/bin/pest --no-coverage`
3. **Verify composer dependencies** can still be installed from scratch

### API Integration Testing
The package integrates with Autotrader's API. For manual validation:
1. **Check configuration structure** in `config/autotrader.php`
2. **Verify trait organization** in `src/Traits/` - each API function group has its own trait
3. **Validate exception handling** in `src/Exceptions/` - proper error handling hierarchy exists

### Test Coverage Validation
Current test structure includes:
- **Feature tests**: 12 test files covering API integrations
- **Unit tests**: 8 test files covering classes, enums, exceptions, and traits
- **Expected results**: ~47 passing tests, some failures due to incomplete mocking (normal)

## Common Tasks

### Repository Structure
```
src/
├── AutotraderApi.php                    # Main API class - orchestrates all functionality
├── AutotraderApiServiceProvider.php     # Laravel service provider
├── Enum/                                # API enums (7 files)
├── Exceptions/                          # Exception hierarchy (6 files)  
├── Traits/                              # API functionality traits (17 files)
└── Validators/                          # Request validators (2 files)

tests/
├── Feature/                             # Integration tests (12 files)
├── Unit/                                # Unit tests (8 files)
├── Traits/                              # Trait-specific tests
├── Pest.php                             # Pest configuration
└── TestCase.php                         # Base test case

config/
└── autotrader.php                       # Package configuration
```

### Key Files and Their Purpose
- **`src/AutotraderApi.php`**: Main entry point, uses trait composition for API methods
- **`src/Traits/`**: Modular API functionality - each trait handles specific API areas
- **`tests/TestCase.php`**: Base test class with Laravel Testbench setup
- **`composer.json`**: Defines PHP 8.4 requirement and Pest dependencies
- **`phpunit.xml`**: Test configuration (works with Pest)

### Environment Configuration
The package requires these environment variables:
- `AUTOTRADER_ENVIRONMENT`: Either 'sandbox' or 'production'
- `AUTOTRADER_KEY`: API key from Autotrader
- `AUTOTRADER_SECRET`: API secret from Autotrader

### Development Patterns
- **Trait-based architecture**: Each API endpoint group has its own trait
- **Exception hierarchy**: All custom exceptions extend `AutotraderException`
- **Laravel integration**: Uses service provider for dependency injection
- **Test isolation**: All tests use HTTP mocking via Laravel's `Http::fake()`

## Known Issues and Workarounds

### PHP Version Compatibility
- **Issue**: Package requires PHP 8.4, but PHP 8.3 is commonly available
- **Workaround**: Temporarily modify composer.json for development, but ALWAYS revert before committing
- **Command**: `composer config --no-plugins allow-plugins.pestphp/pest-plugin true` must be run before installation

### Test Suite Status
- **Current state**: Some tests are incomplete (marked as 'todo') or failing due to missing mocks
- **Expected**: ~47 passing tests, ~24 failing, ~4 todos - this is normal for current codebase
- **Do NOT modify tests to make them pass** - focus on functionality, not test suite completion

### Network Dependencies
- **Issue**: Composer installation may experience timeouts during dependency downloads
- **Workaround**: Use generous timeouts (600+ seconds) and retry if needed
- **Never cancel** long-running composer operations

### No CI/CD Pipeline
- **Issue**: No `.github/workflows/` directory found
- **Impact**: No automated testing or code quality checks
- **Recommendation**: Run manual validation before making changes

## Development Guidelines

### Code Style
- Follow existing patterns in the codebase
- Use PHP 8.4 features and syntax where appropriate
- Maintain trait-based architecture for new API functionality
- Use proper type hints and return types

### Testing Requirements
- **All tests must use Pest PHP** - PHPUnit is not available
- Use `Http::fake()` for mocking API calls in feature tests
- Follow existing test organization in `tests/Feature/` and `tests/Unit/`
- Tests are located in parallel with source structure

### Adding New Features
1. Create appropriate trait in `src/Traits/` for new API functionality
2. Add trait to main `AutotraderApi` class
3. Create feature tests with proper HTTP mocking
4. Add unit tests for any new validators or exceptions
5. Update documentation in README.md if needed

## Timeout Values and Expectations

| Command | Expected Time | Timeout Setting | Notes |
|---------|--------------|-----------------|-------|
| `composer install` | 62 seconds | 600+ seconds | NEVER CANCEL - network dependent |
| `./vendor/bin/pest` | 1.5 seconds | 300+ seconds | NEVER CANCEL - may vary with code changes |
| `php -l src/*.php` | <1 second | 30 seconds | Syntax validation is fast |

**CRITICAL**: Always use generous timeouts and NEVER cancel long-running operations. Build and test processes may take longer in different environments.

## Frequently Used Commands (Copy-Paste Ready)

### Clean Installation from Scratch
```bash
# Remove existing installation
rm -rf vendor/ composer.lock .phpunit.cache/

# Check PHP version (should be 8.4+)
php --version

# For PHP 8.3 environments only - modify composer.json temporarily
sed -i 's/"php": "\^8.4"/"php": "^8.3"/' composer.json

# Enable Pest plugin and install
composer config --no-plugins allow-plugins.pestphp/pest-plugin true
timeout 600 composer install --no-interaction

# Run tests to verify installation
timeout 300 ./vendor/bin/pest --no-coverage

# Check syntax of all source files
find src/ -name "*.php" -exec php -l {} \;

# Revert composer.json if modified
sed -i 's/"php": "\^8.3"/"php": "^8.4"/' composer.json
```

### Quick Health Check
```bash
# Verify all key components
php --version                                    # Check PHP
composer --version                              # Check Composer
ls -la src/AutotraderApi.php                   # Check main class exists
ls -la tests/TestCase.php                      # Check test base exists
./vendor/bin/pest --version                    # Check Pest works
```

## Troubleshooting

### "PHP version does not satisfy requirement"
- **Problem**: Composer fails with PHP version error
- **Solution**: Follow the PHP 8.3 workaround in prerequisites section
- **Always remember**: Revert composer.json changes before committing

### "Pest plugin blocked by allow-plugins"
- **Problem**: Composer installation fails due to plugin restrictions
- **Solution**: Run `composer config --no-plugins allow-plugins.pestphp/pest-plugin true`
- **When**: Run this before every fresh installation

### "Connection timeout during installation"
- **Problem**: Network timeouts during dependency download
- **Solution**: Use longer timeouts (600+ seconds) and retry
- **Never**: Cancel or interrupt composer operations

### Tests failing with mocking errors
- **Current status**: Expected behavior - many tests have incomplete mocks
- **Expected results**: ~47 pass, ~24 fail, ~4 todos
- **Action required**: None - focus on functionality, not making all tests pass