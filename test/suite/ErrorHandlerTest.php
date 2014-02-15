<?php

namespace Psr\Error;

use ErrorException;
use PHPUnit_Framework_TestCase;

class ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->errorReporting = error_reporting(E_DEPRECATED);
    }

    protected function tearDown()
    {
        error_reporting($this->errorReporting);
    }

    public function handleErrorData()
    {
        //                               severity             isHandled
        return array(
            // standard severities - handled
            'Error'             => array(E_ERROR,             true),
            'User error'        => array(E_USER_ERROR,        true),
            'Recoverable error' => array(E_RECOVERABLE_ERROR, true),
            'Warning'           => array(E_WARNING,           true),
            'User warning'      => array(E_USER_WARNING,      true),
            'Notice'            => array(E_NOTICE,            true),
            'User notice'       => array(E_USER_NOTICE,       true),

            // standard severities - not handled
            'Deprecated'        => array(E_DEPRECATED,        false),
            'User deprecated'   => array(E_USER_DEPRECATED,   false),

            // uncommon severities
            'Parse error'       => array(E_PARSE,             true),
            'Core error'        => array(E_CORE_ERROR,        true),
            'Core warning'      => array(E_CORE_WARNING,      true),
            'Compile error'     => array(E_COMPILE_ERROR,     true),
            'Compile warning'   => array(E_COMPILE_WARNING,   true),
            'Strict'            => array(E_STRICT,            true),
            'All'               => array(E_ALL,               true),
        );
    }

    /**
     * @dataProvider handleErrorData
     */
    public function testHandleError($severity, $isHandled)
    {
        $handler = new ErrorHandler;
        $error = null;
        $wasHandled = null;
        try {
            $wasHandled = $handler($severity, 'Error message.', '/path/to/file', 111);
        } catch (ErrorException $error) {}

        if ($isHandled) {
            $this->assertEquals(new ErrorException('Error message.', 0, $severity, '/path/to/file', 111), $error);
        } else {
            $this->assertFalse($wasHandled);
        }
    }

    /**
     * @dataProvider handleErrorData
     */
    public function testHandleErrorAtSuppression($severity, $isHandled)
    {
        $handler = new ErrorHandler;
        error_reporting(0);

        $this->assertFalse($handler($severity, 'Error message.', '/path/to/file', 111));
    }
}
