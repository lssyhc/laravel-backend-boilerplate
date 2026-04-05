<?php

declare(strict_types=1);

use App\Models\User;

/*
|--------------------------------------------------------------------------
| POST /api/auth/login
|--------------------------------------------------------------------------
*/

describe('POST /api/auth/login', function () {

    // ── Happy Path ──────────────────────────────────────────────────────

    it('logs in with valid credentials', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                    'token',
                ],
            ])
            ->assertJsonPath('data.user.email', 'john@example.com')
            ->assertJsonPath('message', 'Login successful.');
    });

    it('returns a non-empty token upon login', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertOk();
        expect($response->json('data.token'))->not->toBeEmpty();
    });

    it('does not expose password or remember_token in response', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonMissingPath('data.user.password')
            ->assertJsonMissingPath('data.user.remember_token');
    });

    // ── Unauthorized (401) ──────────────────────────────────────────────

    it('fails with wrong password', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401)
            ->assertJsonPath('message', 'The provided credentials are incorrect.');
    });

    it('fails with nonexistent email', function () {
        $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ])->assertStatus(401)
            ->assertJsonPath('message', 'The provided credentials are incorrect.');
    });

    // ── Forbidden (403) ─────────────────────────────────────────────────────────

    it('fails with unverified email', function () {
        User::factory()->unverified()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ])->assertStatus(403)
            ->assertJsonPath('message', 'Your email address is not verified.');
    });    // ── Validation Errors (422) ─────────────────────────────────────────

    it('fails without email', function () {
        $this->postJson('/api/auth/login', [
            'password' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('fails without password', function () {
        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });

    it('fails with invalid email format', function () {
        $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            'password' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('fails when all fields are empty', function () {
        $this->postJson('/api/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    });

});
