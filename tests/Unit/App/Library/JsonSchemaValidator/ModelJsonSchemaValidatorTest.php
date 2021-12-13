<?php

namespace Tests\Unit\Library\JsonSchemaValidator;

use App\Library\JsonSchemaValidator\ModelJsonSchemaValidatorTrait;
use App\Library\JsonSchemaValidator\ValidationError;
use Tests\TestCase;

class ModelJsonSchemaValidatorTest extends TestCase
{
    private object $testModelContainsSchema;
    private object $testModelContainsSchemaFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testModelContainsSchema = new class {
            use ModelJsonSchemaValidatorTrait;

            private const POST_SCHEMA = '
                {
                    "$schema": "http://json-schema.org/draft-07/schema#",
                    "type": "object",
                    "properties": {
                        "param": {
                            "type": "string",
                            "maxLength": 12
                        }
                    },
                    "required": ["param"]
                }
            ';
            private string $param;

            public function setParam(string $param): void
            {
                $this->param = $param;
            }
        };

        $this->testModelContainsSchemaFile = new class {
            use ModelJsonSchemaValidatorTrait;

            private const POST_SCHEMA_PATH = __DIR__ . '/test_schema.json';
            private string $param;

            public function setParam(string $param): void
            {
                $this->param = $param;
            }
        };
    }

    /**
     * @test
     */
    public function validateNotThrowException()
    {
        $this->expectNotToPerformAssertions();

        $this->testModelContainsSchema->setParam('valid value');
        $this->testModelContainsSchema->validate(ModelJsonSchemaValidatorTrait::$OPERATION_POST);
    }

    /**
     * @test
     */
    public function validateWithSchemaFileNotThrowException()
    {
        $this->expectNotToPerformAssertions();

        $this->testModelContainsSchemaFile->setParam('valid value');
        $this->testModelContainsSchemaFile->validate(ModelJsonSchemaValidatorTrait::$OPERATION_POST);
    }

    /**
     * @test
     */
    public function validateThrowValidationError()
    {
        $this->expectException(ValidationError::class);

        $this->testModelContainsSchema->setParam('this is invalid value');
        $this->testModelContainsSchema->validate(ModelJsonSchemaValidatorTrait::$OPERATION_POST);
    }

    /**
     * @test
     */
    public function validateWithSchemaFileThrowValidationError()
    {
        $this->expectException(ValidationError::class);

        $this->testModelContainsSchemaFile->setParam('this is invalid value');
        $this->testModelContainsSchemaFile->validate(ModelJsonSchemaValidatorTrait::$OPERATION_POST);
    }
}
