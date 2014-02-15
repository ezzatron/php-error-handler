# Error handling

This document describes standard behaviors for PHP error handlers. In addition,
it details a means by which projects can specify their error handling
requirements.

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119].

## 1. Specification

### 1.1. Concepts

- Two standard error handling methods are defined in this document. 'error
  exception' handling and 'legacy' handling.
- It is RECOMMENDED that new code be written to expect the 'Exception' error
  handling method.

### 1.x. Error exception handling

### 1.x. Legacy error handling

### 1.x. `Psr\Error\ErrorHandlerInterface`

```php
<?php

namespace Psr\Error;

/**
 * The interface implemented by error handlers.
 */
interface ErrorHandlerInterface
{
    /**
     * Handle a PHP error.
     *
     * @param integer $severity   The severity of the error.
     * @param string  $message    The error message.
     * @param string  $filename   The filename in which the error was raised.
     * @param integer $lineNumber The line number in which the error was raised.
     *
     * @return boolean                 True if the error was handled, otherwise false.
     * @throws ErrorExceptionInterface Representing the error, unless the error is a deprecation message, or '@' suppression is in use.
     */
    public function handleError($severity, $message, $filename, $lineNumber);
}
```

### 1.x. `Psr\Error\ErrorExceptionInterface`

```php
<?php

namespace Psr\Error;

use Exception;

/**
 * The interface implemented by error exceptions.
 *
 * In addition, implementations MUST be an exception. That is, they MUST extend
 * from one of PHP's built-in exception types.
 */
interface ErrorExceptionInterface
{
    /**
     * Get the error severity.
     *
     * @return integer The error severity.
     */
    public function getSeverity();

    /**
     * Get the error message.
     *
     * @return string The error message.
     */
    public function getMessage();

    /**
     * Get the filename in which the error was raised.
     *
     * @return string The filename.
     */
    public function getFile();

    /**
     * Get the line number in which the error was raised.
     *
     * @return integer The line number.
     */
    public function getLine();

    /**
     * Get the stack trace for the error.
     *
     * @return array The stack trace.
     */
    public function getTrace();

    /**
     * Get the stack trace for the error as a string.
     *
     * @return string The stack trace.
     */
    public function getTraceAsString();

    /**
     * Get the string representation of the error.
     *
     * @return string The string representation.
     */
    public function __toString();
}
```

<!-- References -->

[RFC 2119]: http://tools.ietf.org/html/rfc2119
