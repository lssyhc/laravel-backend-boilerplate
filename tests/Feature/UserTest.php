<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| GET /api/user
|--------------------------------------------------------------------------
*/

describe('GET /api/user', function () {

    // ── Happy Path ──────────────────────────────────────────────────────

    it('returns authenticated user profile', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
            ])
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('message', 'Success');
    });

    it('does not expose password or remember_token', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.remember_token');
    });

    // ── Unauthorized (401) ──────────────────────────────────────────────

    it('rejects unauthenticated request', function () {
        $this->getJson('/api/user')
            ->assertStatus(401);
    });

    it('rejects request with invalid token', function () {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/user')
            ->assertStatus(401);
    });

});
