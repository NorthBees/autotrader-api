# Contributing

Contributions are welcome and will be fully credited.

Contributions are accepted via Pull Requests on [Github](https://github.com/northbees/autotrader-api).

## Development Requirements

Before contributing, please ensure your development environment meets these requirements:

### PHP Version
- **PHP 8.4 is required** - All code must be compatible with PHP 8.4
- Use PHP 8.4 features and syntax where appropriate
- Ensure backward compatibility within the 8.4 range

### Testing Framework
- **All tests must be written in Pest PHP** - We use Pest PHP as our primary testing framework
- Follow existing test patterns and organization:
  - `tests/Feature/` for integration and feature tests
  - `tests/Unit/` for unit tests
  - `tests/Traits/` for trait-specific tests
- Use Laravel's HTTP fake for mocking API calls
- Ensure tests are descriptive and cover edge cases

### Code Style and UX
- **Match existing system patterns** - Code style and UX style should be consistent with the existing codebase
- Follow Laravel package development conventions
- Maintain fluent, Laravel-style API interfaces
- Use PSR-4 autoloading standards
- Implement proper type hints and return types
- Follow existing naming conventions and structure
- Preserve backward compatibility unless explicitly breaking changes are needed

## Pull Request Guidelines

-   **Add tests!** - Your patch won't be accepted if it doesn't have tests written in Pest PHP.

-   **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

-   **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

-   **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

-   **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

-   **Follow PHP 8.4 standards** - Ensure your code uses PHP 8.4 compatible syntax and features.

-   **Test coverage** - Ensure your changes maintain or improve test coverage.

## Development Setup

1. Clone the repository
2. Ensure you have PHP 8.4 installed
3. Run `composer install` to install dependencies
4. Run `composer test` to execute the test suite
5. Run `composer lint` to check code style
6. Run `composer analyse` to run static analysis

## Code Quality

- Run `composer lint` before submitting - code must pass [Laravel Pint](https://laravel.com/docs/pint) checks
- Run `composer analyse` - code must pass [PHPStan](https://phpstan.org/) static analysis
- Follow existing code organization patterns
- Use the trait-based architecture for new API functionality
- Implement proper exception handling using the existing exception hierarchy
- Add appropriate validation for new features
- Document your code with clear, concise comments where necessary

## Things you could do

If you want to contribute but do not know where to start, this list provides some starting points.

-   Improve test coverage
-   Add support for new Autotrader API endpoints
-   Enhance error handling and validation
-   Improve documentation and examples
-   Performance optimizations

**Happy coding**!
