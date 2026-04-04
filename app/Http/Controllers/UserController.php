<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        return $this->successResponse(
            data: new UserResource($user),
        );
    }
}
