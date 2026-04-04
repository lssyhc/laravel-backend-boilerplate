<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RefreshTokenAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

final class RefreshTokenController extends Controller
{
    public function __invoke(Request $request, RefreshTokenAction $action): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) { // @phpstan-ignore instanceof.alwaysTrue
            $token->delete();
        }

        $newToken = $action->execute($user);

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'token' => $newToken->plainTextToken,
            ],
            message: 'Token refreshed successfully.',
        );
    }
}
