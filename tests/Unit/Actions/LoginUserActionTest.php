<?php

use App\Actions\Auth\LoginUserAction;
use App\DTOs\Auth\LoginUserData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;

describe('LoginUserAction', function () {

    it('returns the user with valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $action = new LoginUserAction;

        $data = new LoginUserData(
            email: 'john@example.com',
            password: 'password',
        );

        $result = $action->execute($data);

        expect($result)->toBeInstanceOf(User::class)
            ->and($result->id)->toBe($user->id)
            ->and($result->email)->toBe('john@example.com');
    });

    it('throws InvalidCredentialsException with wrong password', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $action = new LoginUserAction;

        $data = new LoginUserData(
            email: 'john@example.com',
            password: 'wrong-password',
        );

        $action->execute($data);
    })->throws(InvalidCredentialsException::class);

    it('throws InvalidCredentialsException with nonexistent email', function () {
        $action = new LoginUserAction;

        $data = new LoginUserData(
            email: 'nonexistent@example.com',
            password: 'password',
        );

        $action->execute($data);
    })->throws(InvalidCredentialsException::class);

});
