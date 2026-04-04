<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

final class LogoutUserAction
{
    public function execute(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) { // @phpstan-ignore instanceof.alwaysTrue
            $token->delete();
        }
    }
}
