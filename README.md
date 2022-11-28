# Laravel Testable requests

A Laravel package to make request testing easier

This package is based on [a Gist](https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d) from [Colin DeCarlo](https://github.com/colindecarlo).

## Installation
You can install this package with composer:

```shell
composer require --dev markwalet/laravel-testable-requests
```

## Documentation

The documentation is still been worked on. For now, please look at how these classes are used by Colin himself in a [Laracon Online talk in 2021](https://youtu.be/mC-MbQSHWec)

### Changes made in comparison to the video:
- Improved type hinting
- Added `defaultData()` method to the request
- Added `assertFailsValidationFor()` method to the validation result.
