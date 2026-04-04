<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUserAction;
use App\DTOs\Auth\LoginUserData;
use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserAction $action): JsonResponse
    {
        $user = $action->execute(LoginUserData::fromRequest($request));

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'token' => $user->createToken(
                    TokenAbility::TOKEN_NAME,
                    TokenAbility::values(),
                    now()->addMinutes((int) config('sanctum.expiration', 1440)), // @phpstan-ignore cast.int
                )->plainTextToken,
            ],
            message: 'Login successful.',
        );
    }
}
