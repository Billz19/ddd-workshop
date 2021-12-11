<?php

namespace App\Packages\Exceptions;

class ResourceAlreadyExistsError extends DbErrorException
{
    public const MSG_PREFIX = "resource already exists";
}
