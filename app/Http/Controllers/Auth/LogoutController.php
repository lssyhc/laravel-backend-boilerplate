<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LogoutUserAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUserAction $action): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        $action->execute($user);

        return $this->successResponse(
            message: 'Successfully logged out.',
        );
    }
}
