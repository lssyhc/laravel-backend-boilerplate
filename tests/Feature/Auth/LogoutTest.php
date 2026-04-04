<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| POST /api/auth/logout
|--------------------------------------------------------------------------
*/

describe('POST /api/auth/logout', function () {

    // ── Happy Path ──────────────────────────────────────────────────────

    it('logs out an authenticated user', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Successfully logged out.')
            ->assertJsonPath('data', null);
    });

    it('revokes the token after logout', function () {
        $user = User::factory()->create();
        $token = $user->createToken('auth');

        $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    });

    // ── Unauthorized (401) ──────────────────────────────────────────────

    it('rejects unauthenticated user', function () {
        $this->postJson('/api/auth/logout')
            ->assertStatus(401);
    });

    it('rejects request with invalid token', function () {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->postJson('/api/auth/logout')
            ->assertStatus(401);
    });

});
