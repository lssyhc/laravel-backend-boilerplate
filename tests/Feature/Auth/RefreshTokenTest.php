<?php

declare(strict_types=1);

use App\Enums\TokenAbility;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| POST /api/auth/refresh
|--------------------------------------------------------------------------
*/

describe('POST /api/auth/refresh', function () {

    // ── Happy Path ──────────────────────────────────────────────────────

    it('refreshes the token for an authenticated user', function () {
        $user = User::factory()->create();
        $token = $user->createToken(TokenAbility::TOKEN_NAME, TokenAbility::values());

        $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson('/api/auth/refresh');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                    'token',
                ],
            ])
            ->assertJsonPath('message', 'Token refreshed successfully.');

        expect($response->json('data.token'))->not->toBeEmpty();
    });

    it('revokes the old token after refresh', function () {
        $user = User::factory()->create();
        $token = $user->createToken(TokenAbility::TOKEN_NAME, TokenAbility::values());

        $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson('/api/auth/refresh')
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    });

    it('returns a different token than the original', function () {
        $user = User::factory()->create();
        $token = $user->createToken(TokenAbility::TOKEN_NAME, TokenAbility::values());

        $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson('/api/auth/refresh');

        $response->assertOk();
        expect($response->json('data.token'))->not->toBe($token->plainTextToken);
    });

    // ── Unauthorized (401) ──────────────────────────────────────────────

    it('rejects unauthenticated user', function () {
        $this->postJson('/api/auth/refresh')
            ->assertStatus(401);
    });

    it('rejects request with invalid token', function () {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->postJson('/api/auth/refresh')
            ->assertStatus(401);
    });

});
