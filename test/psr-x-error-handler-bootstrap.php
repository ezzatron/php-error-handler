<?php

/**
 * Example error handler bootstrapping code.
 */

require __DIR__ . '/../vendor/autoload.php';

set_error_handler(new Psr\Error\ErrorHandler);
