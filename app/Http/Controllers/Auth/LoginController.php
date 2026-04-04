<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\CreateTokenAction;
use App\Actions\Auth\LoginUserAction;
use App\DTOs\Auth\LoginUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserAction $loginAction, CreateTokenAction $createTokenAction): JsonResponse
    {
        $user = $loginAction->execute(LoginUserData::fromRequest($request));
        $token = $createTokenAction->execute($user);

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
            ],
            message: 'Login successful.',
        );
    }
}
