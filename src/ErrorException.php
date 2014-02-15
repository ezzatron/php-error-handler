<?php

namespace Psr\Error;

use ErrorException as NativeErrorException;

/**
 * Represents a PHP error.
 */
class ErrorException extends NativeErrorException implements
    ErrorExceptionInterface
{
    /**
     * Construct a new PHP error exception.
     *
     * @param integer $severity   The severity of the error.
     * @param string  $message    The error message.
     * @param string  $filename   The filename in which the error was raised.
     * @param integer $lineNumber The line number in which the error was raised.
     */
    public function __construct($severity, $message, $filename, $lineNumber)
    {
        parent::__construct($message, 0, $severity, $filename, $lineNumber);
    }
}
