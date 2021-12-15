<?php

namespace Tests\Unit\App\Packages\Posts\Models;


use App\Packages\Exceptions\InvalidArgumentError;
use App\Packages\Posts\Models\Post;
use App\Packages\Posts\Models\PostCollection;
use Tests\Data\Fixtures\PostFixture;
use Tests\TestCase;

class PostCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function createCollection()
    {
        $post1 = PostFixture::newPost(withId: true)->toArray();
        $post2 = PostFixture::newPost(withId: true)->toArray();

        $postCollection = new PostCollection([$post1, $post2]);

        $this->assertContainsOnly(Post::class, $postCollection->getData());
        $this->assertEqualsCanonicalizing(
            [$post1, $post2],
            $postCollection->toArray()
        );
    }

    /**
     * @test
     */
    public function createCollectionThrowInvalidTypeError()
    {
        $this->expectException(InvalidArgumentError::class);

        new PostCollection([['attr' => 'value']]);
    }
}
