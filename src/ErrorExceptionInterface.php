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
