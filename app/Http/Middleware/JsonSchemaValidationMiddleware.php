<?php

namespace App\Http\Middleware;

use App\Services\JsonSchemaValidator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class JsonSchemaValidationMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $validator  = new JsonSchemaValidator();
        $controller = Route::current()->controller;

        if(defined($controller::class . '::BODY_SCHEMA_PATH')) {
            $validator->validateRequestWithSchemaFile($request->all(), $controller::BODY_SCHEMA_PATH);
        }
        elseif(defined($controller::class . '::BODY_SCHEMA')) {
            $validator->validateRequest($request->all(), $controller::BODY_SCHEMA);
        }

        if(defined($controller::class . '::PARAM_SCHEMA_PATH')) {
            $validator->validateRequestWithSchemaFile($request->route()->parameters(), $controller::PARAM_SCHEMA_PATH);
        }
        elseif(defined($controller::class . '::PARAM_SCHEMA')) {
            $validator->validateRequest($request->route()->parameters(),$controller::PARAM_SCHEMA);
        }

        return $next($request);
    }
}
