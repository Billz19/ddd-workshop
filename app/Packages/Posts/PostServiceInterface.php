<?php

namespace App\Packages\Posts;

use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;

interface PostServiceInterface {

    public function createPost(Post $post): Post;
    public function updatePost(Post $post): Post;
    public function deletePost(string $postId): void;
    public function getPost(string $postId, string $authUserId): Post;
    public function getPosts(PostsQuery $postsQuery, string $authUserId): PostCollection;

}
