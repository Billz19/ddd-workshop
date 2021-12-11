<?php

namespace App\Exceptions;

use App\Http\Exceptions\RequestError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        //transform any @RequestError into a well formatted error response
        if($request->wantsJson() && $e instanceof RequestError) {
            return $this->responseFromException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * Returns @JsonResponse that contains errors and stack trace of the error when debug is enabled.
     */
    private function responseFromException(RequestError $e) : JsonResponse
    {
        $data = ['errors' => $e->getErrors()];

        if(env('APP_DEBUG', false)) {
            $data['debug'] = ['stacktrace' => $e->getFormattedTrace()];
        }

        return response()->json($data, $e->getHTTPCode());
    }
}
