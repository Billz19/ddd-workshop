<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\BadRequestError;
use App\Http\Exceptions\InternalServerError;
use App\Library\JsonSchemaValidator\ValidationError;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Posts\Models\Post;
use App\Packages\Posts\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreatePostController extends Controller
{

    public function __construct(
        private PostService $postService,
    )
    {
    }


    public function __invoke(Request $request)
    {
        try {
            $userId = auth()->user()->getId();
            $post = Post::fromArray($request->all());
            $post->setCreator($userId);
            $result = $this->postService->createPost($post);
            return response()->json($result, Response::HTTP_CREATED);
        } catch (ResourceAlreadyExistsError $e) {
            throw new BadRequestError(errors: $e->getMessage(), previous: $e);
        } catch (ValidationError $e) {
            throw new BadRequestError(errors: $e->getErrors(), previous: $e);
        } catch (\Exception $e) {
            throw new InternalServerError(errors: $e->getMessage(), previous: $e);
        }

    }
}
