<?php

namespace Tests\Unit\App\Library\Request;

use App\Library\Request\IdsQuery;
use App\Library\Serialize\ArraySerializableTrait;
use Tests\TestCase;

class IdsQueryTest extends TestCase
{

    private object $testClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testClass = new class implements \JsonSerializable {
            use IdsQuery;
            use ArraySerializableTrait;
        };
    }
    /**
     * @test
     */
    public function serialize()
    {
        $inputData = ['ids' => ['id1','id2']];
        $instance = $this->testClass::fromArray($inputData);
        $this->assertEqualsCanonicalizing($inputData, $instance->toArray());
    }
}
