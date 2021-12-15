<?php

namespace Tests\Feature\App\Http\Controllers\Api\Posts;

use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use App\Packages\Users\Models\User;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\Data\Fixtures\PostFixture;
use Tests\Data\Fixtures\UserFixture;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\Helpers\ArangoPostInitDbTrait;
use Tests\Helpers\MockingTrait;
use Tests\TestCase;

/**
 * @group Posts
 */
class GetPostControllerTest extends TestCase
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
     * @group GetPost
     * @group GetPost::Controller
     */
    public function testGetPost()
    {
        $post = PostFixture::newPost(withId: true);
        $this->mockAndBindRepository(['getPost' => $post], PostArangoRepository::class);
        $response = $this->getJson(self::API_PREFIX_LINK . "/{$post->getId()}");
        $response->assertOk();
        $response->assertJson($post->toArray());
    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Controller
     */
    public function tesGetPostThrowResourceNotFound()
    {
        $this->triggerRepositoryException('getPost', ResourceNotFoundError::class, PostArangoRepository::class);
        $response = $this->getJson(self::API_PREFIX_LINK . '/not_found_post');
        $response->assertNotFound();
    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Controller
     */
    public function tesGetPostThrowInternalServerError()
    {
        $this->triggerRepositoryException('getPost', UnknownDBErrorException::class, PostArangoRepository::class);
        $response = $this->getJson(self::API_PREFIX_LINK . '/post_id');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
