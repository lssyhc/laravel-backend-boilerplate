<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class EmailNotVerifiedException extends HttpException
{
    public function __construct()
    {
        parent::__construct(
            statusCode: Response::HTTP_FORBIDDEN,
            message: 'Your email address is not verified.',
        );
    }

    public function report(): bool
    {
        return false;
    }
}
