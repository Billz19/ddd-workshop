<?php


namespace App\Http\Exceptions;


use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class RequestError extends \Error
{
    protected array $errors;

    public function __construct(string|array $errors = "" , $code = 0, Throwable $previous = null)
    {
        if (is_array($errors)) {
            $this->errors = $errors;
        } else {
            $this->errors = [$errors];
        }
        parent::__construct(Response::$statusTexts[$this->getHTTPCode()], $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return a file:line format of stack trace.
     */
    public function getFormattedTrace(): array
    {
        return array_map(
            fn($t) => $t['file'] . ':' . $t['line'],
            $this->getTrace()
        );
    }

    abstract function getHTTPCode() : int;
}
