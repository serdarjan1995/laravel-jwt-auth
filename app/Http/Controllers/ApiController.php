<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function success($message = null, $data = null)
    {
        return $this->makeJSONResponse(JsonResponse::HTTP_OK, $message, $data, false);
    }

    protected function badRequest($message = null)
    {
        return $this->makeJSONResponse(JsonResponse::HTTP_BAD_REQUEST, $message, null, true);
    }

    protected function notFound($message = null)
    {
        return $this->makeJSONResponse(JsonResponse::HTTP_NOT_FOUND, $message, null, true);
    }

    protected function unprocessableEntity($message = null)
    {
        return $this->makeJSONResponse(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $message, null, true);
    }

    protected function forbidden($message = null)
    {
        return $this->makeJSONResponse(JsonResponse::HTTP_FORBIDDEN, $message, null, true);
    }

    protected function unauthorized($message = null)
    {
        return $this->makeJSONResponse(JsonResponse::HTTP_UNAUTHORIZED, $message, null, true);
    }

    public function makeJSONResponse(int $status = 200, string $message = null, array $data = null, bool $error = false)
    {
        return response()->json([
            "status" => JsonResponse::$statusTexts[$status],
            "error" => $error,
            "message" => $message,
            'data' => $data
        ], $status)
            ->header("Locale", app()->getLocale());
    }
}
