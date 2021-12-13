<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\InternalServerError;
use App\Http\Exceptions\UnauthorizedError;
use App\Http\Requests\UserLoginRequest;
use App\Packages\Exceptions\InvalidArgumentError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LoginController extends Controller
{

    public function __construct(
        private UserService $userService,
    )
    {
    }

    /**
     * Login user.
     *
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function __invoke(UserLoginRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->findByEmail($request->get('email'));

            if (!password_verify($request->get('password'), $user->getPassword())) {
                throw new InvalidArgumentError('Wrong password');
            }

            auth()->setUser($user);

            return response()->json(['token' => auth()->user()->createToken('API Token')->plainTextToken],Response::HTTP_OK);

        } catch (ResourceNotFoundError | InvalidArgumentError $e) {
            throw new UnauthorizedError($e->getMessage(), $e);
        } catch (\Exception $e) {
            throw new InternalServerError($e->getMessage(), $e);
        }

    }
}

