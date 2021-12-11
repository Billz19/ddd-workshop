<?php

namespace App\Packages\Exceptions;

class ResourceNotFoundError extends DbErrorException
{
    public const MSG_PREFIX = "resource not found";
}
