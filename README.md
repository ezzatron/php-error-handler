# RFC: PHP error handler

*An attempt at a PSR-style standards document detailing a standard PHP error
handler.*

[![Current build status image][build-image]][Current build status]
[![Current coverage status image][coverage-image]][Current coverage status]

## The standards documents

- The main error handler spec: [error-handler.md](error-handler.md).
- Justification and other info: [error-handler-meta.md](error-handler-meta.md).

## Running the test suite

- `composer install`
- `vendor/bin/archer`

[PHPUnit] is required to be in `PATH`.

<!-- References -->

[PHPUnit]: http://phpunit.de/

[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/ezzatron/php-error-handling/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/ezzatron/php-error-handling
[coverage-image]: http://img.shields.io/coveralls/ezzatron/php-error-handling/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/ezzatron/php-error-handling
