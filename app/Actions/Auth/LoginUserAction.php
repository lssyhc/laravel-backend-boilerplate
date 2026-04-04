<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginUserData;
use App\Exceptions\Auth\EmailNotVerifiedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class LoginUserAction
{
    /**
     * @throws InvalidCredentialsException
     * @throws EmailNotVerifiedException
     */
    public function execute(LoginUserData $data): User
    {
        $user = User::where('email', $data->email)->first();

        if (! $user instanceof User) {
            // Prevent timing-based user enumeration
            Hash::make('timing-attack-prevention');

            throw new InvalidCredentialsException;
        }

        if (! Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        if (! $user->hasVerifiedEmail()) {
            throw new EmailNotVerifiedException;
        }

        return $user;
    }
}
