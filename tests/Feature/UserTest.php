<?php

declare(strict_types=1);

use App\Enums\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| GET /api/v1/user
|--------------------------------------------------------------------------
*/

describe('GET /api/v1/user', function () {

    // ── Happy Path ──────────────────────────────────────────────────────

    it('returns authenticated user profile', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user, TokenAbility::values());

        $response = $this->getJson('/api/v1/user');

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

        Sanctum::actingAs($user, TokenAbility::values());

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.remember_token');
    });

    // ── Unauthorized (401) ──────────────────────────────────────────────

    it('rejects unauthenticated request', function () {
        $this->getJson('/api/v1/user')
            ->assertStatus(401);
    });

    it('rejects request with invalid token', function () {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/v1/user')
            ->assertStatus(401);
    });

    it('rejects token without required ability', function () {
        $user = User::factory()->create();

        $token = $user->createToken('limited', ['some:other:ability']);

        $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->getJson('/api/v1/user')
            ->assertStatus(403);
    });

});
