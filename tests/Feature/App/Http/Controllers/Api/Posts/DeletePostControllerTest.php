<?php

namespace Tests\Feature\App\Http\Controllers\Api\Posts;

use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use App\Packages\Posts\Repository\PostRepositoryInterface;
use App\Packages\Users\Models\User;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\Data\Fixtures\UserFixture;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\Helpers\ArangoPostInitDbTrait;
use Tests\Helpers\MockingTrait;
use Tests\TestCase;

/**
 * @group Posts
 */
class DeletePostControllerTest extends TestCase
{
    use ArangoConnectionTrait;
    use ArangoPostInitDbTrait;
    use MockingTrait;

    const API_PREFIX_LINK = 'api/v1/posts';

    private User $auth_user;

    protected function setUp(): void
    {
        parent::setUp();

        // handle sanctum auth
        Sanctum::actingAs(UserFixture::newUser(withId: true), ['*']);
        $this->bindPostsRepository();
    }

    /**
     * @test
     * @group DeletePost
     * @group DeletePost::Controller
     */
    public function testDeletePost()
    {
        $this->mockAndBindRepository(['deletePost' => null], PostRepositoryInterface::class);
        $response = $this->deleteJson(self::API_PREFIX_LINK . "/post_id");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }


    /**
     * @test
     * @group DeletePost
     * @group DeletePost::Controller
     */
    public function testDeletePostThrowsInternalServerError()
    {
        $this->triggerRepositoryException('getPost', UnknownDBErrorException::class, PostRepositoryInterface::class);
        $response = $this->deleteJson(self::API_PREFIX_LINK . '/post_id');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
