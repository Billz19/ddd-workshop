<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\InternalServerError;
use App\Packages\Exceptions\InvalidArgumentError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Posts\PostService;
use App\Packages\Posts\PostsQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetPostsController extends Controller
{

    public function __construct(
        private PostService $postService,
    )
    {
    }


    public function __invoke(Request $request)
    {
        try {
            $query = PostsQuery::fromArray([
                'page' => $request->get('page', 1),
                'perPage' => $request->get('per_page', 20),
            ]);

            $posts = $this->postService->getPosts($query);
            return response()
                ->json($posts, Response::HTTP_OK)
                ->withHeaders($this->getHeaders($query));
        } catch (\Exception $e) {
            throw new InternalServerError($e->getMessage(), $e);
        }

    }

    /**
     * Get headers for response.
     */
    private function getHeaders(PostsQuery $postsQuery): array
    {
        return [
            'X-Total-Pages' => $postsQuery->getTotalPages(),
            'X-Per-Page'    => $postsQuery->getPerPage(),
            'X-Next-Page'   => $postsQuery->getNextPage(),
            'X-Prev-Page'   => $postsQuery->getPrevPage(),
            'X-Page'        => $postsQuery->getPage(),
            'X-Total'       => $postsQuery->getTotal(),
            'Access-Control-Expose-Headers' => '*'
        ];
    }
}
