<?php

namespace App\Http\Services;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomErrorService
{
    /**
     * @param      $message
     * @param      $errors
     * @param  int $status
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function failedApiResponse($message, $errors, $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
