<?php

namespace Tests\Unit\App\Packages\Users\Models;

use App\Packages\Users\Models\User;
use Tests\TestCase;

/**
 * @group Users
 */
class UserTest extends TestCase
{
    /**
     * @test
     */
    public function serialize()
    {
        $inputData = [
            'name'   => 'John Smith',
            'password' => 'password',
            'email'  => 'email',
        ];
        $outputFromArray = User::fromArray($inputData);
        $outputToArray = $outputFromArray->toArray();
        $this->assertInstanceOf(User::class, $outputFromArray);
        unset($inputData['password'],$outputToArray['password']);
        $this->assertEqualsCanonicalizing($inputData, $outputToArray);
    }
}
