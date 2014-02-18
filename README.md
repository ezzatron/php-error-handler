# RFC: PHP error handling

*An attempt at a PSR-style standards document detailing PHP error handling best
practices.*

[![Current build status image][build-image]][Current build status]
[![Current coverage status image][coverage-image]][Current coverage status]

## Installation and documentation

* Available as [Composer] package [ezzatron/php-error-handling].
* [API documentation] available.

## The standards document

See [error-handler.md](error-handler.md).

## Running the test suite

- `composer install`
- `vendor/bin/archer`

[PHPUnit] is required to be in `PATH`.

<!-- References -->

[PHPUnit]: http://phpunit.de/

[API documentation]: http://lqnt.co/php-error-handling/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/ezzatron/php-error-handling/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/ezzatron/php-error-handling
[coverage-image]: http://img.shields.io/coveralls/ezzatron/php-error-handling/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/ezzatron/php-error-handling
