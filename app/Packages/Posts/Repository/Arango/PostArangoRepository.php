<?php


namespace App\Packages\Posts\Repository\Arango;

use App\Library\ArangoDb\ArangoErrorCodes;
use App\Library\ArangoDb\ArangoTrait;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;
use App\Packages\Posts\Repository\PostRepositoryInterface;
use App\Packages\Posts\Repository\PostsQuery;
use ArangoDBClient\Connection as ArangoConnection;

class PostArangoRepository implements PostRepositoryInterface
{
    use ArangoTrait;


    public function __construct(
        private ArangoConnection $connection,
    )
    {
    }

    public function createPost(Post $post): Post
    {
        $json = json_encode($post->toArray());
        try {
            $result = $this->executeQuery(
                query: "INSERT ${json} INTO @@posts RETURN {post: MERGE({id: NEW._key}, NEW)}",
                bindVars: ['@posts' => PostsCollection::COLLECTION]
            )->current()->get('post');

            return Post::fromArray($result);
        } catch (\Exception $e) {
            if ($e->getCode() === ArangoErrorCodes::CONFLICT) {
                throw new ResourceAlreadyExistsError(message: "the post '{$post->getTitle()}' already exists", previous: $e);
            }
            throw new UnknownDBErrorException(message: "could not create post", previous: $e);
        }
    }

    public function updatePost(Post $post): Post
    {
        $postAsJson = json_encode($post);
        $query = "
            FOR post IN @@posts
                FILTER post._key == @postId
                UPDATE post WITH ${postAsJson}
                IN @@posts
                RETURN {post: MERGE({id: NEW._key}, NEW)}
        ";
        try {
            $cursor = $this->executeQuery(
                query: $query,
                bindVars: [
                    '@posts' => PostsCollection::COLLECTION,
                    'postId' => $post->getId()
                ]
            );
            return Post::fromArray($cursor->current()->get('post'));
        } catch (\Exception $e) {
            throw new UnknownDBErrorException(message: "could not update post", previous: $e);
        }
    }

    public function deletePost(string $postId): void
    {
        $query = "
            FOR post IN @@posts
                FILTER post._key == @postId
                REMOVE post IN @@posts
        ";
        $bindVars = [
            '@posts' => PostsCollection::COLLECTION,
            'postId' => $postId,
        ];

        try {
            $this->executeQuery($query, $bindVars);
        } catch (\Exception $e) {
            throw new UnknownDBErrorException("could not delete post 'posts/${postId}'", previous: $e);
        }
    }

    public function getPost(string $postId): Post
    {
        try {
            $query = "
                FOR post IN @@posts
                  FILTER post._key == @postId
                  RETURN {result: MERGE({id: post._key}, post)}
            ";
            $cursor = $this->executeQuery(
                $query,
                [
                    '@posts' => PostsCollection::COLLECTION,
                    'postId' => $postId,
                ]
            );
        } catch (\Exception $e) {
            throw new UnknownDBErrorException(message: "could not get post", previous: $e);
        }
        if ($cursor->getCount() === 0) {
            throw new ResourceNotFoundError("the resource 'posts/${postId}' does not exist");
        }

        $result = $cursor->current()->get('result');
        return Post::fromArray($result);

    }

    public function getPosts(PostsQuery $postsQuery): PostCollection
    {
        $query = "
            FOR post IN @@posts
                LIMIT @start, @limit
                RETURN MERGE({id: post._key}, post)
        ";
        $bindVars = [
            '@posts' => PostsCollection::COLLECTION,
            'start' => $postsQuery->getStart(),
            'limit' => $postsQuery->getLimit(),
        ];
        try {
            $cursor = $this->executeQuery($query, $bindVars);

            return new PostCollection(
                array_map(
                    fn($document) => $document->getAll(),
                    $cursor->getAll()
                )
            );
        } catch (\Exception $e) {
            throw new UnknownDBErrorException("could not get posts", previous: $e);
        }
    }

    public function getPostsCount(): int
    {
        try {
            $query = "
                RETURN LENGTH(@@posts)
            ";
            return (int)$this->executeQuery(
                $query,
                [
                    '@posts' => PostsCollection::COLLECTION,
                ]
            )->current();
        } catch (\Exception $e) {
            throw new UnknownDBErrorException(message: "could not get posts count", previous: $e);
        }
    }
}
