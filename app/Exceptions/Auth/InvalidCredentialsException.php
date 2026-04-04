<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InvalidCredentialsException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'The provided credentials are incorrect.',
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function report(): bool
    {
        return false;
    }
}
