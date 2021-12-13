<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\BadRequestError;
use App\Http\Exceptions\InternalServerError;
use App\Http\Requests\UserRegisterRequest;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Users\Models\User;
use App\Packages\Users\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{

    public function __construct(
        private UserService $userService,
    )
    {
    }

    /**
     * Register a new user.
     *
     * @param UserRegisterRequest $request
     * @return JsonResponse
     */
    public function __invoke(UserRegisterRequest $request)
    {
        try {
            $user = $this->userService->create(User::fromArray($request->all()));
            return response()->json([
                'token' => $user->createToken('API Token')->plainTextToken
            ], Response::HTTP_CREATED);
        } catch (ResourceAlreadyExistsError $e) {
            throw new BadRequestError(errors: $e->getMessage(), previous: $e);
        } catch (\Exception $e) {
            throw new InternalServerError(errors: $e->getMessage(), previous: $e);
        }
    }
}
