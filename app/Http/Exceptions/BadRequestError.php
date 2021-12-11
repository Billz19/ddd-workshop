<?php


namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BadRequestError extends RequestError
{
    function getHTTPCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
