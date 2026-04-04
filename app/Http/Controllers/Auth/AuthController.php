<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\LogoutUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\LoginUserData;
use App\DTOs\Auth\RegisterUserData;
use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        RegisterUserAction $action,
    ): JsonResponse {
        $user = $action->execute(RegisterUserData::fromRequest($request));

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('auth', [TokenAbility::All->value])->plainTextToken,
        ], Response::HTTP_CREATED);
    }

    public function login(
        LoginRequest $request,
        LoginUserAction $action,
    ): JsonResponse {
        $user = $action->execute(LoginUserData::fromRequest($request));

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('auth', [TokenAbility::All->value])->plainTextToken,
        ]);
    }

    public function logout(
        Request $request,
        LogoutUserAction $action,
    ): JsonResponse {
        /** @var User $user */
        $user = $request->user();

        $action->execute($user);

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }
}
