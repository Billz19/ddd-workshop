<?php


namespace App\Library\JsonSchemaValidator;

use App\Packages\Exceptions\InvalidArgumentError;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\Validator;

/**
 * Trait @ModelJsonSchemaValidator implement logic for validate models data.
 */
trait ModelJsonSchemaValidatorTrait
{
    use GetErrorsTrait;

    public static string $OPERATION_GET      = 'GET';
    public static string $OPERATION_PUT      = 'PUT';
    public static string $OPERATION_POST     = 'POST';
    public static string $OPERATION_PATCH    = 'PATCH';
    public static string $OPERATION_RESPONSE = 'RESPONSE';

    /**
     * Run the validation process and throw @ValidationError exception if data not valid.
     *
     * @throws ValidationError
     */
    public function validate(string $operation): void
    {
        $this->validateWithJsonSchema($operation);
    }

    /**
     * Run the json schema validation process and throw @ValidationError exception if data not valid.
     *
     * @throws ValidationError
     */
    private function validateWithJsonSchema(string $operation): void
    {
        $validator  = new Validator();
        $schema     = $this->getSchema($operation);
        if($schema === "") {
            return;
        }
        $schema     = Schema::fromJsonString($schema);
        $result     = $validator->schemaValidation(
            data: $this->prepareData(),
            schema: $schema,
            max_errors: 1000
        );

        if($result->hasErrors()) {
            $resultErrors = $result->getErrors();
            $errors       = $this->getErrors($resultErrors);
            throw new ValidationError($errors);
        }
    }

    /**
     * Return the appropriate schema for the $operation.
     *
     * @throws InvalidArgumentError
     */
    private function getSchema(string $operation): string
    {
        if(defined(static::class . "::${operation}_SCHEMA_PATH")) {
            $schema = $this->loadSchemaFromFile(
                constant(static::class . "::${operation}_SCHEMA_PATH")
            );
        }
        elseif(defined(static::class . "::${operation}_SCHEMA")) {
            $schema = constant(static::class . "::${operation}_SCHEMA");
        }

        return $schema ?? '';
    }

    /**
     * Return data as an object if @toArray() is implemented than call it
     * otherwise call @get_object_vars()
     */
    private function prepareData(): object
    {
        return (object) ($this instanceof \JsonSerializable
            ? json_decode(json_encode($this))
            : get_object_vars($this));
    }

    /**
     * Read schema from file exists in $path.
     * it's throws InvalidArgumentError if file does not exists .
     *
     * @throws InvalidArgumentError
     */
    private function loadSchemaFromFile(string $path): string
    {
        if (! file_exists($path)) {
            throw new InvalidArgumentError(message: "file '$path' does not exists");
        }
        return file_get_contents($path);
    }
}
