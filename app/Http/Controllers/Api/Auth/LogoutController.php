<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LogoutController extends Controller
{

    /**
     * Logout user
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json(null, Response::HTTP_OK);
    }
}

