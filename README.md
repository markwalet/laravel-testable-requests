# Laravel Testable requests

[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Stable Version](https://poser.pugx.org/markwalet/laravel-testable-requests/v/stable)](https://packagist.org/packages/markwalet/laravel-git-state)
[![Total Downloads](https://poser.pugx.org/markwalet/laravel-testable-requests/downloads)](https://packagist.org/packages/markwalet/laravel-git-state)

A Laravel package to make request testing easier.

This package is based on [a Gist](https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d) from [Colin DeCarlo](https://github.com/colindecarlo).

## Installation
You can install this package with composer:

```shell
composer require --dev markwalet/laravel-testable-requests
```

## Documentation

The documentation is still being worked on. For now, please look at how these classes are used by Colin himself in a [Laracon Online talk in 2021](https://youtu.be/mC-MbQSHWec)

### Changes made in comparison to the video:
- Improved type hinting
- Added `defaultData()` method to the request
- Added `assertFailsValidationFor()` method to the validation result.
