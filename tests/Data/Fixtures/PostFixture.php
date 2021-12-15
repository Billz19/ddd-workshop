<?php

namespace Tests\Data\Fixtures;

use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;
use Faker\Factory;

class PostFixture
{
    public static function newPost(bool $withId = false): Post
    {
        $faker = Factory::create('en_GB');
        $userArray = [
            'title' => $faker->sentence(3),
            'content' => $faker->text(100),
            'imageUrl' => $faker->url,
        ];

        if ($withId) {
            $userArray['id'] = uniqid();
        }

        return Post::fromArray($userArray);
    }

    public static function newPostCollection($count = 2): PostCollection
    {
        $posts = [];
        for ($i = 0; $i < $count; $i++) {
            $posts[] = self::newPost(withId: true)->toArray();
        }

        return new PostCollection($posts);
    }
}
