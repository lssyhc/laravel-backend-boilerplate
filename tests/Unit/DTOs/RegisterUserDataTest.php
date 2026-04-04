<?php

use App\DTOs\Auth\RegisterUserData;
use App\Http\Requests\Auth\RegisterRequest;

describe('RegisterUserData', function () {

    it('creates an instance via constructor', function () {
        $data = new RegisterUserData(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
        );

        expect($data->name)->toBe('John Doe')
            ->and($data->email)->toBe('john@example.com')
            ->and($data->password)->toBe('password');
    });

    it('creates an instance from a request', function () {
        $request = RegisterRequest::create('/api/auth/register', 'POST', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $request->setContainer(app());
        $request->validateResolved();

        $data = RegisterUserData::fromRequest($request);

        expect($data)->toBeInstanceOf(RegisterUserData::class)
            ->and($data->name)->toBe('Jane Doe')
            ->and($data->email)->toBe('jane@example.com')
            ->and($data->password)->toBe('secret123');
    });

    it('is readonly', function () {
        $data = new RegisterUserData(
            name: 'John',
            email: 'john@example.com',
            password: 'password',
        );

        $reflection = new ReflectionClass($data);

        expect($reflection->isReadOnly())->toBeTrue();
    });

});
