<?php


namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UnauthorizedError extends RequestError
{
    function getHTTPCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
