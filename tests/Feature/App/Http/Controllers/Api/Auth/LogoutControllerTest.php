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
class LogoutControllerTest extends TestCase
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
    public function testLogout()
    {
        $user = User::fromArray(['email' => 'test.84@gmail.com', 'password' => 'strongestPassEver89', 'id' => uniqid()]);
        $this->mockAndBindRepository(['findUserByEmail' => $user]);

        $response = $this->postJson(self::API_PREFIX_LINK . '/login', [
            'password' => 'strongestPassEver89',
            'email' => $user->getEmail()
        ]);

        $token = $response->json('data.token');

        $response = $this->postJson(self::API_PREFIX_LINK . '/logout',[], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertOk();
    }
}
