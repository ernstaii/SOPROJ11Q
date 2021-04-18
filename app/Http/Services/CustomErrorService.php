<?php

namespace App\Http\Services;

use Illuminate\Http\Response;

class CustomErrorService
{
    public static function failedApiResponse($message, $errors, $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
