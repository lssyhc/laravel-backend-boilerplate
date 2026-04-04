<?php

declare(strict_types=1);

use App\Exceptions\Auth\EmailNotVerifiedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
|--------------------------------------------------------------------------
| EmailNotVerifiedException
|--------------------------------------------------------------------------
*/

describe('EmailNotVerifiedException', function () {

    it('extends HttpException', function () {
        $exception = new EmailNotVerifiedException;

        expect($exception)->toBeInstanceOf(HttpException::class);
    });

    it('has 403 status code', function () {
        $exception = new EmailNotVerifiedException;

        expect($exception->getStatusCode())->toBe(403);
    });

    it('has correct error message', function () {
        $exception = new EmailNotVerifiedException;

        expect($exception->getMessage())->toBe('Your email address is not verified.');
    });

    it('does not report to exception handler', function () {
        $exception = new EmailNotVerifiedException;

        expect($exception->report())->toBeFalse();
    });

});
