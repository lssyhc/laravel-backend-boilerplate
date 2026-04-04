<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterUserData;
use App\Models\User;

final class RegisterUserAction
{
    public function execute(RegisterUserData $data): User
    {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);
    }
}
