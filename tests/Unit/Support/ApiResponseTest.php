<?php

declare(strict_types=1);

use App\Support\ApiResponse;

/*
|--------------------------------------------------------------------------
| ApiResponse Trait
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->responder = new class
    {
        use ApiResponse {
            successResponse as public;
            errorResponse as public;
        }
    };
});

describe('ApiResponse', function () {

    it('returns success response with default values', function () {
        $response = $this->responder->successResponse();

        expect($response->getStatusCode())->toBe(200);

        $data = $response->getData(true);
        expect($data['message'])->toBe('Success')
            ->and($data['data'])->toBeNull();
    });

    it('returns success response with custom data and message', function () {
        $response = $this->responder->successResponse(
            data: ['key' => 'value'],
            message: 'Custom message',
            status: 201,
        );

        expect($response->getStatusCode())->toBe(201);

        $data = $response->getData(true);
        expect($data['message'])->toBe('Custom message')
            ->and($data['data'])->toMatchArray(['key' => 'value']);
    });

    it('returns error response with default values', function () {
        $response = $this->responder->errorResponse();

        expect($response->getStatusCode())->toBe(500);

        $data = $response->getData(true);
        expect($data['message'])->toBe('Something went wrong')
            ->and($data)->not->toHaveKey('errors');
    });

    it('returns error response with errors', function () {
        $response = $this->responder->errorResponse(
            message: 'Validation failed',
            errors: ['field' => ['Error message']],
            status: 422,
        );

        expect($response->getStatusCode())->toBe(422);

        $data = $response->getData(true);
        expect($data['message'])->toBe('Validation failed')
            ->and($data['errors'])->toMatchArray(['field' => ['Error message']]);
    });

});
