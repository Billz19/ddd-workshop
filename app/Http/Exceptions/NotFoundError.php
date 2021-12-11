<?php


namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotFoundError extends RequestError
{
    public function __construct(string $error = 'resource not found' , $code = 0, Throwable $previous = null)
    {
        parent::__construct($error, $code, $previous);
    }

    function getHTTPCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
