<?php

namespace App\Packages\Posts\Repository;

use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;

interface PostRepositoryInterface {

    public function getPostsCount(): int;
    public function createPost(Post $post): Post;
    public function updatePost(Post $post): Post;
    public function deletePost(string $postId): void;
    public function getPost(string $postId): Post;
    public function getPosts(PostsQuery $postsQuery): PostCollection;

}
