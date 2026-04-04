<?php

declare(strict_types=1);

use App\Exceptions\Auth\InvalidCredentialsException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
|--------------------------------------------------------------------------
| InvalidCredentialsException
|--------------------------------------------------------------------------
*/

describe('InvalidCredentialsException', function () {

    it('extends HttpException', function () {
        $exception = new InvalidCredentialsException;

        expect($exception)->toBeInstanceOf(HttpException::class);
    });

    it('has 401 status code', function () {
        $exception = new InvalidCredentialsException;

        expect($exception->getStatusCode())->toBe(401);
    });

    it('has correct error message', function () {
        $exception = new InvalidCredentialsException;

        expect($exception->getMessage())->toBe('The provided credentials are incorrect.');
    });

    it('does not report to exception handler', function () {
        $exception = new InvalidCredentialsException;

        expect($exception->report())->toBeFalse();
    });

});
