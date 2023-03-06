<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ResponseController extends Controller
{

    /*
     * Most common response types are made as reusable functions
     */

    public static function emptySuccess(): JsonResponse
    {
        return response()->json([
            'code' => 200
        ]);
    }

    public static function successWithMessage($created_title, $created): JsonResponse
    {
        return response()->json([
            'code' => 201,
            $created_title => $created
        ], 201);
    }

    public static function errorWithMessage($message): JsonResponse
    {
        return response()->json([
            'code' => 500,
            'error' => $message
        ], 500);
    }

    public static function validationFail($violations): JsonResponse
    {
        return response()->json([
            'code' => 400,
            'violations' => $violations
        ], 400);
    }

    public static function invalidCredentials(): JsonResponse
    {
        return response()->json([
            'code' => 401,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public static function invalidAuthor(): JsonResponse
    {
        return response()->json([
            'code' => '401',
            'message' => 'You are not the author of this task'
        ], 401);
    }
}
