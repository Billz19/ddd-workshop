<?php

namespace Tests\Feature\App\Http\Controllers\Api\Posts;

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
class GetPostsControllerTest extends TestCase
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
     * @group GetPosts
     * @group GetPosts::Controller
     */
    public function testGetPosts()
    {
        $count = 3;
        $posts = PostFixture::newPostCollection($count);
        $this->mockAndBindRepository(['getPostsCount' => $count, 'getPosts' => $posts], PostArangoRepository::class);
        $response = $this->getJson(self::API_PREFIX_LINK . '?page=1&perPage=20');
        $response->assertOk();
        $response->assertJson($posts->toArray());
    }

    /**
     * @test
     * @group GetPosts
     * @group GetPosts::Controller
     */
    public function testGetPostsThrowInternalServerError()
    {
        $mockListsRepository = \Mockery::mock(PostArangoRepository::class);
        $mockListsRepository
            ->shouldReceive('getPostsCount')
            ->andReturn(3);
        $mockListsRepository
            ->shouldReceive('getPosts')
            ->andThrow(UnknownDBErrorException::class);

        $response = $this->getJson(self::API_PREFIX_LINK . '?page=1&perPage=20');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
