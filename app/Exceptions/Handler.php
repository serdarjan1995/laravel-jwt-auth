<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        //
    }

    protected function renderHttpException(HttpExceptionInterface $e)
    {
        if ($e instanceof NotFoundHttpException) {
            return $this->makeJSONResponse(JsonResponse::HTTP_NOT_FOUND,
                "Endpoint does not exists",
                null,
                true);
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->makeJSONResponse(JsonResponse::HTTP_METHOD_NOT_ALLOWED,
                "Provided HTTP method is not supported on this endpoint",
                null,
                true);
        }
        if ($e instanceof UnauthorizedHttpException) {
            return $this->makeJSONResponse(JsonResponse::HTTP_UNAUTHORIZED,
                "You are unauthorized to make requests to this endpoint",
                null,
                true);
        }

        $data = null;
        if (env('APP_DEBUG')){
            $data = ['error_stack' => $e];
        }
        return $this->makeJSONResponse(JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            "Something went wrong",
            $data,
            true);
    }

    public function makeJSONResponse(int $status = 500, string $message = null, array $data = null, bool $error = true)
    {
        return response()->json([
            "status" => JsonResponse::$statusTexts[$status],
            "error" => $error,
            "message" => $message,
            'data' => $data
        ],$status)
            ->header("Locale", app()->getLocale());
    }
}
