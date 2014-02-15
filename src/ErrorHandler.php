<?php

namespace Psr\Error;

/**
 * Handles PHP errors by converting them to error exceptions.
 */
class ErrorHandler implements ErrorHandlerInterface
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
    public function handleError($severity, $message, $filename, $lineNumber)
    {
        if (E_DEPRECATED === $severity || E_USER_DEPRECATED === $severity) {
            return false;
        }
        if (0 === error_reporting()) {
            return false;
        }

        throw new ErrorException($severity, $message, $filename, $lineNumber);
    }
}
