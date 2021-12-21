<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\InternalServerError;
use App\Http\Exceptions\NotFoundError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Posts\PostService;
use App\Packages\Posts\PostServiceInterface;
use Illuminate\Http\Response;

class GetPostController extends Controller
{

    public function __construct(
        private PostServiceInterface $postService,
    )
    {
    }

    public function __invoke(string $postId)
    {
        try {
            $result = $this->postService->getPost($postId);
            return response()->json($result, Response::HTTP_OK);
        } catch (ResourceNotFoundError $e) {
            throw new NotFoundError(error: $e->getMessage(), previous: $e);
        } catch (\Exception $e) {
            throw new InternalServerError(error: $e->getMessage(), previous: $e);
        }

    }
}
