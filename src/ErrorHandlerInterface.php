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
