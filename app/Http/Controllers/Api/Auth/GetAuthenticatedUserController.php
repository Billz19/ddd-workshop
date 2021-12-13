<?php

namespace App\Http\Controllers\Api\Auth;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetAuthenticatedUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $user = auth()->user();

        return response()->json($user, Response::HTTP_OK);
    }
}

