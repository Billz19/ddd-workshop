<?php

namespace Tests\Unit\App\Library\Serialize;

use App\Library\Serialize\ArraySerializableInterface;
use App\Library\Serialize\ArraySerializableTrait;
use Tests\TestCase;


class ArraySerializableTraitTest extends TestCase
{
    private $invokeToArray;
    private $invokeFromArray;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invokeToArray = new class implements ArraySerializableInterface {
            use ArraySerializableTrait;

            private string $property1;
            private object $property2;
            private array $property4;
            public function __construct()
            {
                $this->property1 = "value 1";
                $this->property2 = new class implements ArraySerializableInterface {
                    use ArraySerializableTrait;
                    private string $property3 = "value 3";
                };
                $this->property4 = [
                    new class implements ArraySerializableInterface  {
                        use ArraySerializableTrait;
                        private string $property5 = "value 5";
                    },
                    'value 6'
                ];
            }
        };

        $this->invokeFromArray = new class implements ArraySerializableInterface {
            use ArraySerializableTrait;

            private string $property1;
            private string $property2;

            public function setProperty1(string $property1) {$this->property1 = $property1; }
            public function setProperty2(string $property2) {$this->property2 = $property2; }
        };
    }

    /**
     * @test
     */
    public function toArray()
    {
        $expectedArray = [
            'property1' => 'value 1',
            'property2' => [
                "property3" => "value 3",
            ],
            "property4" => [
                ["property5" => "value 5"],
                'value 6',
            ],
        ];

        $this->assertEqualsCanonicalizing($expectedArray, $this->invokeToArray->toArray());
    }

    /**
     * @test
     *
     * @depends toArray
     */
    public function fromArray()
    {
        $inputArray = [
            'property1' => 'value 1',
            'property2' => 'value 2',
            'property3' => 'value 3',
        ];
        $expectedArray = [
            'property1' => 'value 1',
            'property2' => 'value 2',
        ];
        $outputObject = $this->invokeFromArray::fromArray($inputArray);

        $this->assertEqualsCanonicalizing($expectedArray, $outputObject->toArray());
        $this->assertFalse(isset($outputObject->property3));
    }
}
