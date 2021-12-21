<?php

namespace Tests\Feature\App\Http\Controllers\Api\Posts;

use App\Http\Exceptions\BadRequestError;
use App\Http\Exceptions\InternalServerError;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use App\Packages\Posts\Repository\PostRepositoryInterface;
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
class CreatePostControllerTest extends TestCase
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
     * @group CreatePost
     * @group CreatePost::Controller
     */
    public function testCreate()
    {
        $post = PostFixture::newPost();
        $this->mockAndBindRepository(['createPost' => $post], PostRepositoryInterface::class);
        $response = $this->postJson(self::API_PREFIX_LINK, $post->toArray());
        $response->assertCreated();
        $response->assertJson($post->toArray());
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Controller
     */
    public function testCreateThrowBadRequestDueValidationError()
    {
        $post = PostFixture::newPost();
        $post->setTitle('tes');
        $response = $this->postJson(self::API_PREFIX_LINK, $post->toArray());
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Controller
     */
    public function testCreateThrowBadRequestDueResourceExistsAlready()
    {
        $this->triggerRepositoryException('createPost', ResourceAlreadyExistsError::class, PostRepositoryInterface::class);
        $post = PostFixture::newPost();
        $response = $this->postJson(self::API_PREFIX_LINK, $post->toArray());
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Controller
     */
    public function testCreateThrowInternalServerError()
    {
        $this->triggerRepositoryException('createPost', UnknownDBErrorException::class, PostRepositoryInterface::class);
        $post = PostFixture::newPost();
        $response = $this->postJson(self::API_PREFIX_LINK, $post->toArray());
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
