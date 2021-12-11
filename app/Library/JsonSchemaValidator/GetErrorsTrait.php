<?php


namespace App\Library\JsonSchemaValidator;

use Opis\JsonSchema\ValidationError as OpisValidationError;


trait GetErrorsTrait
{
    /**
     * Return the validation result errors.
     *
     * @return  array<string>
     */
    private function getErrors(array $errors): array
    {
        $errorsAsString = [];
        foreach ($errors as $error) {
            if(!empty($error->subErrors())) {
                $errorsAsString = array_merge($errorsAsString, $this->getErrors($error->subErrors()));
            } else {
                $errorsAsString[] = $this->formatErrorMessage($error);
            }
        }

        return $errorsAsString;
    }

    /**
     * Return formatted error message.
     */
    private function formatErrorMessage(OpisValidationError $error): string
    {
        $dataPointer = implode('.', $error->dataPointer());
        $keywordArgs = str_replace('"', '', json_encode($error->keywordArgs()));

        return $dataPointer . ': ' . $error->keyword() . ' ' . $keywordArgs;
    }
}
