<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\RegisterUserData;
use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        return DB::transaction(function () use ($request, $action): JsonResponse {
            $user = $action->execute(RegisterUserData::fromRequest($request));

            return $this->successResponse(
                data: [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth', TokenAbility::values())->plainTextToken,
                ],
                message: 'User registered successfully.',
                status: Response::HTTP_CREATED,
            );
        });
    }
}
