<?php

namespace App\Http\Responses;

class ResponseHandler
{
    public static function success($data, $message = '', $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message, $code): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }

    // You can add more methods as needed, for example, a method for responding with a custom status.
}
