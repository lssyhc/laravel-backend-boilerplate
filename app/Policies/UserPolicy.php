<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function view(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id;
    }

    public function update(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id;
    }

    public function delete(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id;
    }
}
