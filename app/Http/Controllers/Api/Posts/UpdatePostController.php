<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\BadRequestError;
use App\Http\Exceptions\InternalServerError;
use App\Library\JsonSchemaValidator\ValidationError;
use App\Packages\Posts\Models\Post;
use App\Packages\Posts\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdatePostController extends Controller
{

    public function __construct(
        private PostService $postService,
    )
    {
    }


    public function __invoke(Request $request, string $postId)
    {
        try {
            $post = Post::fromArray($request->all());
            $post->setId($postId);
            $result = $this->postService->updatePost($post);
            return response()->json($result, Response::HTTP_OK);
        } catch (ValidationError $e) {
            throw new BadRequestError(errors: $e->getErrors(), previous: $e);
        } catch (\Exception $e) {
            throw new InternalServerError($e->getMessage(), $e);
        }

    }
}
