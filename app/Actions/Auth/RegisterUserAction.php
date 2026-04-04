<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterUserData;
use App\Models\User;
use Illuminate\Database\QueryException;

final class RegisterUserAction
{
    /**
     * @throws QueryException
     */
    public function execute(RegisterUserData $data): User
    {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);
    }
}
