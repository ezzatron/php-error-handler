<?php

namespace Psr\Error;

use ErrorException;

/**
 * Handles PHP errors by converting them to error exceptions.
 */
class ErrorHandler
{
    /**
     * Handle a PHP error.
     *
     * @param integer $severity   The severity of the error.
     * @param string  $message    The error message.
     * @param string  $path       The path to the file in which the error was raised.
     * @param integer $lineNumber The line number in which the error was raised.
     *
     * @return boolean        True if the error was handled, otherwise false.
     * @throws ErrorException Representing the error, unless the error is a deprecation message, or '@' suppression is in use.
     */
    public function __invoke($severity, $message, $path = '', $lineNumber = 0)
    {
        if (E_DEPRECATED === $severity || E_USER_DEPRECATED === $severity) {
            return false;
        }
        if (0 === error_reporting()) {
            return true;
        }

        throw new ErrorException($message, 0, $severity, $path, $lineNumber);
    }
}
