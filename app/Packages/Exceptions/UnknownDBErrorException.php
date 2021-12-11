<?php

namespace App\Packages\Exceptions;

class UnknownDBErrorException extends DbErrorException
{
    public const MSG_PREFIX = 'unknown error';
}
