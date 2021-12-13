<?php

namespace Tests\Feature\App\Http\Controllers\Api\Auth;

use App\Packages\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * @group Users
 */
class GetAuthenticatedUserControllerTest extends TestCase
{

    const API_PREFIX_LINK = 'api/v1/auth/me';

    /**
     * @test
     */
    public function getAuthenticatedUser()
    {
        $user = new User();
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(self::API_PREFIX_LINK);

        $response->assertOk();
        $response->assertJson($user->toArray());
    }

    /**
     * @test
     */
    public function getAuthenticatedUserReturnUnauthorizedCode()
    {
        $response = $this->getJson(self::API_PREFIX_LINK);

        $response->assertUnauthorized();
    }

}
