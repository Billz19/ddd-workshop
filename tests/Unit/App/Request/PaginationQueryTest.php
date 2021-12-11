<?php

namespace Tests\Unit\App\Library\Request;

use App\Library\Request\PaginationQuery;
use App\Library\Serialize\ArraySerializableTrait;
use Tests\TestCase;

class PaginationQueryTest extends TestCase
{

    private object $testClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testClass = new class implements \JsonSerializable {
            use PaginationQuery;
            use ArraySerializableTrait;
        };
    }
    /**
     * @test
     */
    public function serialize()
    {
        $inputData = ['page' => 1, 'perPage' => 5];
        $instance = $this->testClass::fromArray($inputData);
        $this->assertEqualsCanonicalizing($inputData, $instance->toArray());
    }


    /**
     * @test
     */
    public function validatePaginationRangeTest()
    {
        $inputData = ['page' => 1, 'perPage' => 5];
        $instance = $this->testClass::fromArray($inputData);
        $instance->configurePagination(5);
        $expected = [
            'page' => 1,
            'perPage' => 5,
            'start' => 0,
            'limit' => 5,
            'count' => 5
        ];
        $this->assertEqualsCanonicalizing($expected, $instance->toArray());
    }


    /**
     * @test
     */
    public function getPaginationStatusTest()
    {
        $inputData = ['page' => 1, 'perPage' => 5];
        $instance = $this->testClass::fromArray($inputData);
        $instance->configurePagination(5);
        $expectedItems = [
            'totalPages' => 1,
            'nextPage' => 1,
            'prevPage' => 1,
            'total' => 5
        ];

        foreach ($expectedItems as $key => $expectedItem) {
            $methodName = 'get' . ucfirst($key);
            $this->assertEqualsCanonicalizing($expectedItem, $instance->$methodName());
        }
    }
}
