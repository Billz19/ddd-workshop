<?php


namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InternalServerError extends RequestError
{
    function getHTTPCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
