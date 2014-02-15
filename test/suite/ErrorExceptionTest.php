<?php

namespace Psr\Error;

use PHPUnit_Framework_TestCase;

class ErrorExceptionTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->exception = new ErrorException(E_ERROR, 'Error message.', '/path/to/file', 111);
    }

    public function testGetSeverity()
    {
        $this->assertSame(E_ERROR, $this->exception->getSeverity());
    }

    public function testGetMessage()
    {
        $this->assertSame('Error message.', $this->exception->getMessage());
    }

    public function testGetFile()
    {
        $this->assertSame('/path/to/file', $this->exception->getFile());
    }

    public function testGetLine()
    {
        $this->assertSame(111, $this->exception->getLine());
    }

    public function testGetTrace()
    {
        $trace = $this->exception->getTrace();

        $this->assertInternalType('array', $trace);
        $this->assertArrayHasKey(0, $trace);
        $this->assertSame(array('file', 'line', 'function', 'class', 'type', 'args'), array_keys($trace[0]));
    }

    public function testGetTraceAsString()
    {
        $this->assertStringStartsWith('#0 ', $this->exception->getTraceAsString());
    }

    public function testToString()
    {
        $this->assertStringStartsWith(
            "exception 'Psr\Error\ErrorException' with message 'Error message.' in /path/to/file:111\n" .
            "Stack trace:\n" .
            "#0 ",
            strval($this->exception)
        );
    }
}
