<?php

declare(strict_types=1);

use App\DTOs\Auth\LoginUserData;
use App\Http\Requests\Auth\LoginRequest;

describe('LoginUserData', function () {

    it('creates an instance via constructor', function () {
        $data = new LoginUserData(
            email: 'john@example.com',
            password: 'password',
        );

        expect($data->email)->toBe('john@example.com')
            ->and($data->password)->toBe('password');
    });

    it('creates an instance from a request', function () {
        $request = LoginRequest::create('/api/auth/login', 'POST', [
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ]);
        $request->setContainer(app());
        $request->validateResolved();

        $data = LoginUserData::fromRequest($request);

        expect($data)->toBeInstanceOf(LoginUserData::class)
            ->and($data->email)->toBe('jane@example.com')
            ->and($data->password)->toBe('secret123');
    });

    it('is readonly', function () {
        $data = new LoginUserData(
            email: 'john@example.com',
            password: 'password',
        );

        $reflection = new ReflectionClass($data);

        expect($reflection->isReadOnly())->toBeTrue();
    });

});
