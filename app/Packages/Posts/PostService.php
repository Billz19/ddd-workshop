<?php


namespace App\Packages\Posts;


use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;

/**
 * The default PostServiceInterface implementation.
 */
class PostService implements PostServiceInterface
{
    public function __construct(
        private PostArangoRepository $postRepository
    )
    {
    }

    public function createPost(Post $post): Post
    {
        $post->validate(Post::$OPERATION_POST);
        return $this->postRepository->createPost($post);
    }

    public function updatePost(Post $post): Post
    {
        $post->validate(Post::$OPERATION_PUT);
        return $this->postRepository->updatePost($post);
    }

    public function deletePost(string $postId): void
    {
        $this->postRepository->deletePost($postId);
    }

    public function getPost(string $postId, string $authUserId): Post
    {
        return $this->postRepository->getPost($postId, $authUserId);
    }

    public function getPosts(PostsQuery $postsQuery, string $authUserId): PostCollection
    {
        $count = $this->postRepository->getPostsCount($authUserId);
        $postsQuery->configurePagination($count);
        return $this->postRepository->getPosts($postsQuery, $authUserId);
    }
}
