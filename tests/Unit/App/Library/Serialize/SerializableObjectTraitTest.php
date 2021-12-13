<?php

namespace Tests\Unit\App\Library\Serialize;

use App\Library\Serialize\ArraySerializableInterface;
use App\Library\Serialize\ArraySerializableTrait;
use App\Library\Serialize\SerializableObjectTrait;
use Tests\TestCase;


class SerializableObjectTraitTest extends TestCase
{
    private $serializableObjectClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializableObjectClass = new class implements ArraySerializableInterface {
            use SerializableObjectTrait;

            private string $test1;
            private object $test2;

            /**
             * @return string
             */
            public function getTest1(): string { return $this->test1; }

            /**
             * @param string $test1
             */
            public function setTest1(string $test1): void { $this->test1 = $test1; }

            /**
             * @return object
             */
            public function getTest2(): object { return $this->test2; }

            /**
             * @param object $test2
             */
            public function setTest2(object|array $test2): void
            {
                $this->test2 = is_array($test2) ? (object) $test2 : $test2;
            }

            private function getInstancePrivateValues(): array
            {
                return [
                    "test1" => $this->test1,
                    "test2" => [
                        "test" => $this->test2->test
                    ],
                ];
            }
        };
    }

    /**
     * @test
     */
    public function serialize()
    {
        $inputArray = [
            'property1' => 'value 1',
            'property2' => [
                "property3" => "value 3",
            ],
            "property4" => [
                ["property5" => "value 5"],
                'value 6',
            ],
            "test1" => "test 1",
            "test2" => [
                "test" => "test"
            ]
        ];

        $outputFromArray = $this->serializableObjectClass::fromArray($inputArray)->toArray();

        $this->assertEqualsCanonicalizing(
            ksort($inputArray ),
            ksort($outputFromArray)
        );
    }

    /**
     * @test
     */
    public function getValue() {
        $inputArray = [
            'property1' => 'value 1',
            "test2" => [
                "test" => "test"
            ]
        ];

        $outputFromArray = $this->serializableObjectClass::fromArray($inputArray);

        $this->assertEquals($inputArray['property1'], $outputFromArray->property1);
        $this->assertEquals($inputArray['test2']['test'], $outputFromArray->test2->test);
    }
}
