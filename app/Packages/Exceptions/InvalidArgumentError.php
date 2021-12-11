<?php

namespace App\Packages\Exceptions;

use Exception;

class InvalidArgumentError extends Exception
{
    public const MSG_PREFIX = "invalid argument";

    public function __construct($message = null, $code = 0, Exception $e = null)
    {
        $message = !empty($message) ? static::MSG_PREFIX . ": {$message}" :  static::MSG_PREFIX;
        parent::__construct($message, $code, $e);
    }
}
