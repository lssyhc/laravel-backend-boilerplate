<?php

declare(strict_types=1);

use App\Exceptions\Auth\InvalidCredentialsException;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| InvalidCredentialsException
|--------------------------------------------------------------------------
*/

describe('InvalidCredentialsException', function () {

    it('renders as JSON with 401 status', function () {
        $exception = new InvalidCredentialsException;
        $request = Request::create('/api/auth/login', 'POST');

        $response = $exception->render($request);

        expect($response->getStatusCode())->toBe(401);

        $data = $response->getData(true);
        expect($data['message'])->toBe('The provided credentials are incorrect.');
    });

    it('does not report to exception handler', function () {
        $exception = new InvalidCredentialsException;

        expect($exception->report())->toBeFalse();
    });

});
