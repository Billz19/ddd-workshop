<?php

use App\Http\Controllers\Api\Auth\GetAuthenticatedUserController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Posts\CreatePostController;
use App\Http\Controllers\Api\Posts\DeletePostController;
use App\Http\Controllers\Api\Posts\GetPostController;
use App\Http\Controllers\Api\Posts\GetPostsController;
use App\Http\Controllers\Api\Posts\UpdatePostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Auth routes
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', RegisterController::class);
        Route::post('login', LoginController::class);

        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('logout', LogoutController::class);
            Route::get('me', GetAuthenticatedUserController::class);
        });
    });
    // Post routes
    Route::group(['prefix' => 'posts'], function () {
        Route::post('/', CreatePostController::class);
        Route::put('/{postId}', UpdatePostController::class);
        Route::delete('/{postId}', DeletePostController::class);
        Route::get('/', GetPostsController::class);
        Route::get('/{postId}', GetPostController::class);
    });
});
