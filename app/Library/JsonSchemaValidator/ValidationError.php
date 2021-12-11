<?php

namespace App\Library\JsonSchemaValidator;

class ValidationError extends \Exception
{
    public const MSG_PREFIX = "validation error";
    private array $errors;

    /**
     * ValidationError constructor.
     * @param array $errors
     */
    public function __construct(array $errors, $message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct(
            message: static::MSG_PREFIX . ": $message",
            code: $code,
            previous: $previous
        );
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
