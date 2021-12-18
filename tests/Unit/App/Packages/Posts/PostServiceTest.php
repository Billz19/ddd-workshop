<?php

namespace Tests\Unit\App\Packages\Posts;

use App\Library\JsonSchemaValidator\ValidationError;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Posts\PostService;
use App\Packages\Posts\PostsQuery;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use Mockery\MockInterface;
use Tests\Data\Fixtures\PostFixture;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\TestCase;

/**
 * @goup Posts
 */
class PostServiceTest extends TestCase
{
    use ArangoConnectionTrait;

    private MockInterface $mockPostsRepository;
    private PostService $postService;

    protected function setUp(): void
    {
        parent::setUp();
        static::cleanArangoDatabases();

        $this->mockPostsRepository = \Mockery::mock(PostArangoRepository::class);
        $this->postService = new PostService($this->mockPostsRepository);
    }


    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Service
     */
    public function testCreatePost()
    {
        $post = PostFixture::newPost();
        $this->mockPostsRepository
            ->shouldReceive('createPost')
            ->with($post)
            ->andReturn($post);
        $result = $this->postService->createPost($post);
        $this->assertEqualsCanonicalizing($post, $result);
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Service
     */
    public function testCreatePostThrowsValidationError()
    {
        $this->expectException(ValidationError::class);
        $post = PostFixture::newPost();
        $post->setContent('on');
        $this->postService->createPost($post);
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Service
     */
    public function testCreatePostThrowsResourceExistsAlready()
    {
        $this->expectException(ResourceAlreadyExistsError::class);
        $post = PostFixture::newPost();
        $this->mockPostsRepository
            ->shouldReceive('createPost')
            ->with($post)
            ->andThrow(ResourceAlreadyExistsError::class);

        $this->postService->createPost($post);
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Service
     */
    public function testCreatePostThrowsUnknownDBException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $post = PostFixture::newPost();
        $this->mockPostsRepository
            ->shouldReceive('createPost')
            ->with($post)
            ->andThrow(UnknownDBErrorException::class);
        $this->postService->createPost($post);
    }

    /**
     * @test
     * @group GetPosts
     * @group GetPosts::Service
     */
    public function testGetPosts()
    {
        $count = 3;
        $postCollection = PostFixture::newPostCollection($count);
        $postsQuery = PostsQuery::fromArray(['page' => 1, 'perPage' => 20]);
        $this->mockPostsRepository
            ->shouldReceive('getPostsCount')
            ->andReturn($count);

        $this->mockPostsRepository
            ->shouldReceive('getPosts')
            ->andReturn($postCollection);

        $result = $this->postService->getPosts($postsQuery);

        $this->assertEqualsCanonicalizing($postCollection, $result);
    }

    /**
     * @test
     * @group GetPosts
     * @group GetPosts::Service
     */
    public function testGetPostsThrowsUnknownDBException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $postsQuery = PostsQuery::fromArray(['page' => 1, 'perPage' => 20]);
        $this->mockPostsRepository
            ->shouldReceive('getPostsCount')
            ->andReturn(3);

        $this->mockPostsRepository
            ->shouldReceive('getPosts')
            ->andThrow(UnknownDBErrorException::class);

        $this->postService->getPosts($postsQuery);

    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Service
     */
    public function testGetPost()
    {
        $post = PostFixture::newPost(withId: true);
        $this->mockPostsRepository
            ->shouldReceive('getPost')
            ->with($post->getId())
            ->andReturn($post);

        $result = $this->postService->getPost($post->getId());

        $this->assertEqualsCanonicalizing($post, $result);
    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Service
     */
    public function testGetPostThrowsResourceNotFound()
    {
        $this->expectException(ResourceNotFoundError::class);
        $this->mockPostsRepository
            ->shouldReceive('getPost')
            ->andThrow(ResourceNotFoundError::class);

        $this->postService->getPost('not_found_id');
    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Service
     */
    public function testGetPostThrowsUnknownDBErrorException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $this->mockPostsRepository
            ->shouldReceive('getPost')
            ->andThrow(UnknownDBErrorException::class);

        $this->postService->getPost('post_id');
    }

    /**
     * @test
     * @group DeletePost
     * @group DeletePost::Service
     */
    public function testDeletePost()
    {
        $this->doesNotPerformAssertions();

        $this->mockPostsRepository
            ->shouldReceive('deletePost')
            ->with('post_id')
            ->andReturn(null);

        $this->postService->deletePost('post_id');
    }

    /**
     * @test
     * @group DeletePost
     * @group DeletePost::Service
     */
    public function testDeletePostThrowsUnknownDBErrorException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $this->mockPostsRepository
            ->shouldReceive('deletePost')
            ->andThrow(UnknownDBErrorException::class);

        $this->postService->deletePost('post_id');
    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Service
     */
    public function testUpdatePost()
    {
        $post = PostFixture::newPost(withId: true);
        $this->mockPostsRepository
            ->shouldReceive('updatePost')
            ->with($post)
            ->andReturn($post);

        $this->postService->updatePost($post);
    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Service
     */
    public function testUpdatePostThrowsValidationError()
    {
        $this->expectException(ValidationError::class);
        $post = PostFixture::newPost(withId: true);
        $post->setTitle('');
        $this->postService->updatePost($post);
    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Service
     */
    public function testUpdatePostThrowsUnknownDBErrorException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $this->mockPostsRepository
            ->shouldReceive('updatePost')
            ->andThrow(UnknownDBErrorException::class);

        $this->postService->updatePost(PostFixture::newPost(withId: true));
    }
}
