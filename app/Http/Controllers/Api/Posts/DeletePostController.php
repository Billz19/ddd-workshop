<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\InternalServerError;
use App\Packages\Posts\PostService;
use App\Packages\Posts\PostServiceInterface;
use Illuminate\Http\Response;

class DeletePostController extends Controller
{

    public function __construct(
        private PostServiceInterface $postService,
    )
    {
    }


    public function __invoke(string $postId)
    {
        try {
            $this->postService->deletePost($postId);
            return \response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            throw new InternalServerError($e->getMessage(), $e);
        }

    }
}
