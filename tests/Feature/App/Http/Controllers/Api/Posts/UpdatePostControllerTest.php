<?php

namespace Tests\Feature\App\Http\Controllers\Api\Posts;

use App\Http\Exceptions\BadRequestError;
use App\Http\Exceptions\InternalServerError;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
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
class UpdatePostControllerTest extends TestCase
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
     * @group UpdatePost
     * @group UpdatePost::Controller
     */
    public function testUpdate()
    {
        $post = PostFixture::newPost(withId: true);
        $this->mockAndBindRepository(['updatePost' => $post], PostArangoRepository::class);
        $response = $this->putJson(self::API_PREFIX_LINK . '/'. $post->getId(), $post->toArray());
        $response->assertOk();
        $response->assertJson($post->toArray());
    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Controller
     */
    public function testUpdateThrowBadRequestDueValidationError()
    {
        $post = PostFixture::newPost();
        $post->setTitle('tes');
        $response = $this->putJson(self::API_PREFIX_LINK . '/post_id', $post->toArray());
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Controller
     */
    public function testUpdateThrowInternalServerError()
    {
        $this->triggerRepositoryException('updatePost', UnknownDBErrorException::class, PostArangoRepository::class);
        $post = PostFixture::newPost();
        $response = $this->putJson(self::API_PREFIX_LINK . '/post_id', $post->toArray());
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
