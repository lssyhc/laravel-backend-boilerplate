<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\LoginRequest;

final readonly class LoginUserData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        /** @var array{email: string, password: string} $data */
        $data = $request->validated();

        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }
}
