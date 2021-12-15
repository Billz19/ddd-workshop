<?php

namespace Tests\Unit\App\Packages\Posts\Repository\Arango;

use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Posts\Models\PostCollection;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use App\Packages\Posts\Repository\PostsQuery;
use ArangoDBClient\CollectionHandler;
use ArangoDBClient\Connection as ArangoConnection;
use Illuminate\Support\Facades\Config;
use Tests\Data\Fixtures\PostFixture;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\Helpers\ArangoPostInitDbTrait;
use Tests\TestCase;

/**
 * @group Posts
 */
class PostArangoRepositoryTest extends TestCase
{
    use ArangoConnectionTrait;
    use ArangoPostInitDbTrait;

    public const ARANGO_DB_CONFIG = 'database.connections.arangodb';
    public const INVALID_ENDPOINT = 'http://localhost:85555';

    protected function setUp(): void
    {
        parent::setUp();
        // after the environment has been loaded we need to clear all test database before repository get initialized
        static::cleanArangoDatabases();
    }

    private function newRepositoryWithInvalidConnection()
    {
        $config = Config::get(static::ARANGO_DB_CONFIG);
        $config['endpoint'] = static::INVALID_ENDPOINT;

        return new PostArangoRepository(new ArangoConnection($config));
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Repository
     */
    public function testCreatePost()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);
        $colHandler = new CollectionHandler($conn);

        $post = PostFixture::newPost(withId: true);
        $result = $arangoRepository->createPost($post);

        $this->assertTrue($colHandler->has('posts'));
        $this->assertEqualsCanonicalizing(
            $post->toArray(),
            $result->toArray()
        );
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Repository
     */
    public function testCreatePostThrowsResourceAlreadyExistsError()
    {
        $this->expectException(ResourceAlreadyExistsError::class);
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);

        $post = PostFixture::newPost(withId: true);
        $arangoRepository->createPost($post);
        $arangoRepository->createPost($post);
    }

    /**
     * @test
     * @group CreatePost
     * @group CreatePost::Repository
     */
    public function testCreatePostThrowsUnknownDBError()
    {
        $this->expectException(UnknownDBErrorException::class);
        $arangoRepository = $this->newRepositoryWithInvalidConnection();

        $post = PostFixture::newPost(withId: true);
        $arangoRepository->createPost($post);
    }

    /**
     * @test
     * @group GetPosts
     * @group GetPosts::Repository
     */
    public function testGetPostsCount()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);
        $count = 3;

        for ($i = 0; $i < $count; $i++) {
            $post = PostFixture::newPost(withId: true);
            $arangoRepository->createPost($post);
        }

        $result = $arangoRepository->getPostsCount();

        $this->assertEqualsCanonicalizing($count, $result);
    }

    /**
     * @test
     * @group GetPosts
     * @group GetPosts::Repository
     */
    public function testGetPosts()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);
        $count = 3;
        $posts = [];

        for ($i = 0; $i < $count; $i++) {
            $post = PostFixture::newPost(withId: true);
            $posts[] = $arangoRepository->createPost($post)->toArray();
        }
        $postsQuery = PostsQuery::fromArray(['page' => 1, 'perPage' => 20]);
        $postsQuery->configurePagination($count);

        $result = $arangoRepository->getPosts($postsQuery);

        $this->assertEqualsCanonicalizing(new PostCollection($posts), $result);
    }

    /**
     * @test
     * @group GetPosts
     * @group GetPosts::Repository
     */
    public function testGetPostsThrowsUnknownDBException()
    {
        $this->expectException(UnknownDBErrorException::class);

        $arangoRepository = $this->newRepositoryWithInvalidConnection();
        $postsQuery = PostsQuery::fromArray(['page' => 1, 'perPage' => 20]);
        $postsQuery->configurePagination(3);

        $arangoRepository->getPosts($postsQuery);
    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Repository
     */
    public function testGetPost()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);
        $post = PostFixture::newPost();

        $post = $arangoRepository->createPost($post);

        $result = $arangoRepository->getPost($post->getId());

        $this->assertEqualsCanonicalizing($post, $result);
    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Repository
     */
    public function testGetPostThrowsResourceNotFound()
    {
        $this->expectException(ResourceNotFoundError::class);
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);

        $arangoRepository->getPost('not_found_post');

    }

    /**
     * @test
     * @group GetPost
     * @group GetPost::Repository
     */
    public function testGetPostThrowsUnknownDBErrorException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $arangoRepository = $this->newRepositoryWithInvalidConnection();

        $arangoRepository->getPost('post_id');

    }

    /**
     * @test
     * @group DeletePost
     * @group DeletePost::Repository
     */
    public function testDeletePost()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);
        $post = PostFixture::newPost();

        $post = $arangoRepository->createPost($post);

        $arangoRepository->deletePost($post->getId());

        try {
            $arangoRepository->getPost($post->getId());
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof ResourceNotFoundError);
        }
    }

    /**
     * @test
     * @group DeletePost
     * @group DeletePost::Repository
     */
    public function testDeletePostThrowsUnknownDBErrorException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $arangoRepository = $this->newRepositoryWithInvalidConnection();

        $arangoRepository->deletePost('post_id');

    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Repository
     */
    public function testUpdatePost()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new PostArangoRepository($conn);
        $post = PostFixture::newPost();

        $post = $arangoRepository->createPost($post);
        $post->setTitle('updated title');
        $arangoRepository->updatePost($post);
        $result = $arangoRepository->getPost($post->getId());

        $this->assertEqualsCanonicalizing($post, $result);
    }

    /**
     * @test
     * @group UpdatePost
     * @group UpdatePost::Repository
     */
    public function testUpdatePostThrowsUnknownDBErrorException()
    {
        $this->expectException(UnknownDBErrorException::class);
        $arangoRepository = $this->newRepositoryWithInvalidConnection();

        $arangoRepository->updatePost(PostFixture::newPost(withId: true));

    }

}
