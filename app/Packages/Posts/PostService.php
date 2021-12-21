<?php


namespace App\Packages\Posts;


use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use App\Packages\Posts\Repository\PostRepositoryInterface;

/**
 * The default PostServiceInterface implementation.
 */
class PostService implements PostServiceInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    )
    {
    }

    public function createPost(Post $post): Post
    {
        $post->validate(Post::$OPERATION_POST);
        $post->setCreatedAt();
        return $this->postRepository->createPost($post);
    }

    public function updatePost(Post $post): Post
    {
        $post->validate(Post::$OPERATION_PUT);
        $post->setUpdatedAt();
        return $this->postRepository->updatePost($post);
    }

    public function deletePost(string $postId): void
    {
        $this->postRepository->deletePost($postId);
    }

    public function getPost(string $postId): Post
    {
        return $this->postRepository->getPost($postId);
    }

    public function getPosts(PostsQuery $postsQuery): PostCollection
    {
        $count = $this->postRepository->getPostsCount();
        $postsQuery->configurePagination($count);
        return $this->postRepository->getPosts($postsQuery);
    }
}
