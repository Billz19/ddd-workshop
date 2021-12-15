<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\InternalServerError;
use App\Http\Exceptions\NotFoundError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Posts\PostService;
use Illuminate\Http\Response;

class GetPostController extends Controller
{

    public function __construct(
        private PostService $postService,
    )
    {
    }

    public function __invoke(string $postId)
    {
        try {
            $authUserId = auth()->user()->getId();
            $result = $this->postService->getPost($postId, $authUserId);
            return response()->json($result, Response::HTTP_OK);
        } catch (ResourceNotFoundError $e) {
            throw new NotFoundError(error: $e->getMessage(), previous: $e);
        } catch (\Exception $e) {
            throw new InternalServerError(error: $e->getMessage(), previous: $e);
        }

    }
}
