<?php

namespace Tests\Unit\App\Packages\Posts\Models;

use App\Packages\Posts\Models\Post;
use Tests\TestCase;

/**
 * @group Posts
 */
class PostTest extends TestCase
{
    /**
     * @test
     */
    public function serialize()
    {
        $inputData = [
            'title'   => 'title',
            'content' => 'loooong content',
            'imageUrl'  => 'url',
        ];
        $outputFromArray = Post::fromArray($inputData);
        $outputToArray = $outputFromArray->toArray();
        $this->assertInstanceOf(Post::class, $outputFromArray);
        $this->assertEqualsCanonicalizing($inputData, $outputToArray);
    }
}
