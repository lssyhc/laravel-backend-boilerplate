<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * @param  JsonSerializable|array<string, mixed>|null  $data
     */
    protected function successResponse(JsonSerializable|array|null $data = null, string $message = 'Success', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * @param  array<string, mixed>|null  $errors
     */
    protected function errorResponse(string $message = 'Something went wrong', ?array $errors = null, int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        /** @var array<string, mixed> $response */
        $response = ['message' => $message];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
