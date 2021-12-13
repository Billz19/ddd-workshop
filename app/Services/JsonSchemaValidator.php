<?php


namespace App\Services;

use App\Http\Exceptions\BadRequestError;
use App\Library\JsonSchemaValidator\GetErrorsTrait;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;

/**
 * @JsonSchemaValidator class using for handling json schema validation.
 */
class JsonSchemaValidator
{
    use GetErrorsTrait;
    /**
     * Runs the validation on the the incoming HTTP request and throws a @BadRequestError exception if data are not valid.
     *
     * @throws BadRequestError
     */
    public function validateRequestWithSchemaFile(array $data, string $path): void
    {
        $schema = $this->readSchemaFromFile($path);
        $this->validateRequest($data, $schema);
    }

    /**
     * Run the validation process and throw @BadRequestError exception if data not valid.
     *
     * @throws BadRequestError
     */
    public function validateRequest(array $data, string $schema) : void
    {
        $validator  = new Validator();
        $dataObj    = json_decode(json_encode($data));
        $schema     = Schema::fromJsonString($schema);
        $result     = $validator->schemaValidation($dataObj, $schema, 1000);

        if($result->hasErrors()) {
            $resultErrors = $result->getErrors();
            $errors       = $this->getErrors($resultErrors);
            throw new BadRequestError($errors);
        }
    }


    /**
     * Get schema from file.
     */
    private function readSchemaFromFile(string $schemaPath): string
    {
        return file_get_contents(resource_path($schemaPath));
    }
}
