<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

final class CreateTokenAction
{
    public function execute(User $user): NewAccessToken
    {
        return $user->createToken(
            TokenAbility::TOKEN_NAME,
            TokenAbility::values(),
            now()->addMinutes((int) config('sanctum.expiration', 1440)), // @phpstan-ignore cast.int
        );
    }
}
