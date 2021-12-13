<?php

namespace Tests\Feature\App\Http\Controllers\Api\Auth;

use App\Packages\Users\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\Helpers\ArangoUserInitDbTrait;
use Tests\Helpers\MockingTrait;
use Tests\TestCase;

/**
 * @group Users
 */
class RegisterControllerTest extends TestCase
{
    use ArangoConnectionTrait;
    use ArangoUserInitDbTrait;
    use MockingTrait;

    const API_PREFIX_LINK = 'api/v1/auth';

    protected function setUp(): void
    {
        parent::setUp();
        // handle sanctum auth
        Sanctum::actingAs(new User(), ['*']);
        $this->bindUsersRepository();
    }

    /**
     * @return void
     */
    public function testRequestValidation()
    {
        $response = $this->postJson(self::API_PREFIX_LINK . '/register', [
            'name' => 'username',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    /**
     * @return void
     */
    public function testCreateUser()
    {
        $user = User::fromArray(['name' => 'John Doe', 'email' => 'test.84@gmail.com', 'password' => 'strongestPassEver89', 'id' => uniqid()]);
        $this->mockAndBindRepository(['createUser' => $user]);
        $response = $this->withHeaders([
            'accept' => 'application/json',
        ])->post(self::API_PREFIX_LINK . '/register', [
            'name' => $user->getName(),
            'password' => 'strongestPassEver89',
            'email' => $user->getEmail()
        ]);

        $response->assertCreated();
    }
}
