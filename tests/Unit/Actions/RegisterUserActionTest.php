<?php

declare(strict_types=1);

use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\RegisterUserData;
use App\Models\User;
use Illuminate\Database\QueryException;

describe('RegisterUserAction', function () {

    it('creates a user in the database', function () {
        $action = new RegisterUserAction;

        $data = new RegisterUserData(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
        );

        $user = $action->execute($data);

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->name)->toBe('John Doe')
            ->and($user->email)->toBe('john@example.com')
            ->and($user->exists)->toBeTrue();

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    });

    it('hashes the password', function () {
        $action = new RegisterUserAction;

        $data = new RegisterUserData(
            name: 'Jane Doe',
            email: 'jane@example.com',
            password: 'secret123',
        );

        $user = $action->execute($data);

        expect($user->password)->not->toBe('secret123');
    });

    it('throws exception for duplicate email', function () {
        User::factory()->create(['email' => 'john@example.com']);

        $action = new RegisterUserAction;

        $data = new RegisterUserData(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
        );

        $action->execute($data);
    })->throws(QueryException::class);

});
