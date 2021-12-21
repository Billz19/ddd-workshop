<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\BadRequestError;
use App\Http\Exceptions\InternalServerError;
use App\Library\JsonSchemaValidator\ValidationError;
use App\Packages\Posts\Models\Post;
use App\Packages\Posts\PostService;
use App\Packages\Posts\PostServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdatePostController extends Controller
{

    public function __construct(
        private PostServiceInterface $postService,
    )
    {
    }


    public function __invoke(Request $request, string $postId)
    {
        try {
            $post = Post::fromArray($request->all());
            $post->setId($postId);
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $file = $imageFile->move(public_path('/images'), $imageFile->getClientOriginalName());
                $post->setImageUrl($file->getFilename());
            }
            $result = $this->postService->updatePost($post);
            return response()->json($result, Response::HTTP_OK);
        } catch (ValidationError $e) {
            throw new BadRequestError(errors: $e->getErrors(), previous: $e);
        } catch (\Exception $e) {
            throw new InternalServerError($e->getMessage(), $e);
        }

    }
}
