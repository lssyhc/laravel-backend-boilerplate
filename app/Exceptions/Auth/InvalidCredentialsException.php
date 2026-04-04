<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InvalidCredentialsException extends HttpException
{
    public function __construct()
    {
        parent::__construct(
            statusCode: Response::HTTP_UNAUTHORIZED,
            message: 'The provided credentials are incorrect.',
        );
    }

    public function report(): bool
    {
        return false;
    }
}
