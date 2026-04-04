<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CreateTokenAction;
use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\RegisterUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterUserAction $registerAction, CreateTokenAction $createTokenAction): JsonResponse
    {
        return DB::transaction(function () use ($request, $registerAction, $createTokenAction): JsonResponse {
            $user = $registerAction->execute(RegisterUserData::fromRequest($request));
            $token = $createTokenAction->execute($user);

            return $this->successResponse(
                data: [
                    'user' => new UserResource($user),
                    'token' => $token->plainTextToken,
                ],
                message: 'User registered successfully.',
                status: Response::HTTP_CREATED,
            );
        });
    }
}
