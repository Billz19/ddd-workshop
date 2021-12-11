<?php

namespace App\Packages\Exceptions;

use Exception;

class DbErrorException extends Exception
{
    public const MSG_PREFIX = 'DB Error';

    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        $message = !empty($message) ? static::MSG_PREFIX . ": {$message}" :  static::MSG_PREFIX;
        parent::__construct($message, $code, $previous);
    }
}
