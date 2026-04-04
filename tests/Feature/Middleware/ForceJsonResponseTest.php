<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ForceJsonResponse Middleware
|--------------------------------------------------------------------------
*/

describe('ForceJsonResponse Middleware', function () {

    it('returns JSON response for API request without JSON Accept header', function () {
        $this->get('/api/v1/user')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    });

    it('returns JSON validation errors for non-JSON API request', function () {
        $this->post('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    });

});
