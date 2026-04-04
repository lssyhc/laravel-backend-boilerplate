<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidCredentialsException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'The provided credentials are incorrect.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
