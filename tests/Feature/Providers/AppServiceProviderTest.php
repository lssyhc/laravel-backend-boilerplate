<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

/*
|--------------------------------------------------------------------------
| AppServiceProvider
|--------------------------------------------------------------------------
*/

describe('AppServiceProvider', function () {

    it('enables model strictness in non-production', function () {
        expect(Model::preventsLazyLoading())->toBeTrue();
    });

    it('registers api rate limiter', function () {
        $limiter = RateLimiter::limiter('api');

        expect($limiter)->toBeCallable();
    });

    it('configures password defaults', function () {
        $password = Password::defaults();

        expect($password)->toBeInstanceOf(Password::class);
    });

});
