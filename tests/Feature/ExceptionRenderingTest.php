<?php

declare(strict_types=1);

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Exception Rendering
|--------------------------------------------------------------------------
*/

describe('Exception Rendering', function () {

    it('renders 404 for non-existent API route', function () {
        $this->getJson('/api/nonexistent')
            ->assertStatus(404)
            ->assertJsonStructure(['message']);
    });

    it('renders 401 for unauthenticated API request', function () {
        $this->getJson('/api/user')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    });

    it('renders 403 for token without required ability', function () {
        $user = User::factory()->create();
        $token = $user->createToken('limited', ['some:other:ability']);

        $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->getJson('/api/user')
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    });

    it('renders 422 with structured validation errors', function () {
        $this->postJson('/api/auth/login', [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email', 'password'],
            ]);
    });

    it('renders custom HttpException with correct status and message', function () {
        $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ])->assertStatus(401)
            ->assertJson(['message' => 'The provided credentials are incorrect.']);
    });

});
