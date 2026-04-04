<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| POST /api/auth/register
|--------------------------------------------------------------------------
*/

describe('POST /api/auth/register', function () {

    // ── Happy Path ──────────────────────────────────────────────────────

    it('registers a user with valid data', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                    'token',
                ],
            ])
            ->assertJsonPath('data.user.name', 'John Doe')
            ->assertJsonPath('data.user.email', 'john@example.com')
            ->assertJsonPath('message', 'User registered successfully.');

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    });

    it('returns a token upon successful registration', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        expect($response->json('data.token'))->not->toBeEmpty();
    });

    it('hashes the password in database', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        expect($user)->not->toBeNull()
            ->and($user->password)->not->toBe('password');
    });

    it('does not expose password or remember_token in response', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonMissingPath('data.user.password')
            ->assertJsonMissingPath('data.user.remember_token');
    });

    // ── Validation Errors (422) ─────────────────────────────────────────

    it('fails without name', function () {
        $this->postJson('/api/auth/register', [
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['name']);
    });

    it('fails without email', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('fails with invalid email format', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('fails with duplicate email', function () {
        User::factory()->create(['email' => 'john@example.com']);

        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('fails without password', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });

    it('fails without password confirmation', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });

    it('fails with mismatched password confirmation', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });

    it('fails when all fields are empty', function () {
        $this->postJson('/api/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    });

    it('fails when name exceeds max length', function () {
        $this->postJson('/api/auth/register', [
            'name' => str_repeat('a', 256),
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['name']);
    });

});
