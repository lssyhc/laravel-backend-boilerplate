<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginUserData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class LoginUserAction
{
    /**
     * @throws InvalidCredentialsException
     */
    public function execute(LoginUserData $data): User
    {
        $user = User::where('email', $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}
