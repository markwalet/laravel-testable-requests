# Changelog

## [Unreleased](https://github.com/markwalet/laravel-testable-requests/compare/v0.4.1...master)

## [v0.4.1 (2025-03-05)](https://github.com/markwalet/laravel-testable-requests/compare/v0.4.0...v0.4.1)

### Fixed
- Fixed deprecation warnings for PHP 8.4

## [v0.4.0 (2025-02-05)](https://github.com/markwalet/laravel-testable-requests/compare/v0.3.4...v0.4.0)

### Added
- Added support for Laravel 12

### Removed
- Removed support for Laravel 11

## [v0.3.4 (2024-02-05)](https://github.com/markwalet/laravel-testable-requests/compare/v0.3.3...v0.3.4)

### Fixed
- Fix support for Laravel 11.

## [v0.3.3 (2024-02-05)](https://github.com/markwalet/laravel-testable-requests/compare/v0.3.2...v0.3.3)

### Added
- Added support for Laravel 11.

## [v0.3.2 (2023-12-01)](https://github.com/markwalet/laravel-testable-requests/compare/v0.3.1...v0.3.2)

### Added
- Added `assertPassesValidationFor` assertion method

## [v0.3.1 (2023-12-01)](https://github.com/markwalet/laravel-testable-requests/compare/v0.3.0...v0.3.1)

### Fixed
- Added missing cast in `assertFailsValidation`

## [v0.3.0 (2023-12-01)](https://github.com/markwalet/laravel-testable-requests/compare/v0.2.0...v0.3.0)

### Added
- Added direct `laravel/framework` dependency.

### Fixed
- Convert failed rules to snake case when it's not a custom rule class.
- Make assertions more exact by keeping array structure of errors intact.
- Move `phpunit/phpunit` dependency to dev-dependencies.

## [v0.2.0 (2023-12-01)](https://github.com/markwalet/laravel-testable-requests/compare/v0.1.1...v0.2.0)

### Added
- Make it possible to assert a specific rule in `assertFailsValidationFor()`

## [v0.1.1 (2023-03-13)](https://github.com/markwalet/laravel-testable-requests/compare/v0.1.0...v0.1.1)

### Added
- Added PHPUnit 10 support

## [v0.1.0 (2023-03-02)](https://github.com/markwalet/laravel-testable-requests/compare/v0.0.2...v0.1.0)

### Added
- Added Laravel 10 support

## [v0.0.2 (2022-11-28)](https://github.com/markwalet/laravel-testable-requests/compare/v0.0.1...v0.0.2)

### Added
- Added a `defaultData()` method to the request.

## v0.0.1 (2022-11-28)

Initial release based on [a Gist](https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d) from [Colin DeCarlo](https://github.com/colindecarlo).