<?php

declare(strict_types=1);

use App\Actions\Auth\CreateTokenAction;
use App\Actions\Auth\RefreshTokenAction;
use App\Enums\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

describe('RefreshTokenAction', function () {

    it('creates a new access token for the user', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction(new CreateTokenAction);
        $token = $action->execute($user);

        expect($token)->toBeInstanceOf(NewAccessToken::class)
            ->and($token->plainTextToken)->not->toBeEmpty();
    });

    it('deletes the old token during refresh', function () {
        $user = User::factory()->create();
        $oldToken = $user->createToken(TokenAbility::TOKEN_NAME, TokenAbility::values());
        $user->withAccessToken($oldToken->accessToken);

        $action = new RefreshTokenAction(new CreateTokenAction);
        $action->execute($user);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $oldToken->accessToken->id,
        ]);
    });

    it('creates token with correct name', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction(new CreateTokenAction);
        $token = $action->execute($user);

        expect($token->accessToken->name)->toBe(TokenAbility::TOKEN_NAME);
    });

    it('creates token with expiration in the future', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction(new CreateTokenAction);
        $token = $action->execute($user);

        expect($token->accessToken->expires_at)->not->toBeNull()
            ->and($token->accessToken->expires_at->isFuture())->toBeTrue();
    });

    it('handles missing access token gracefully', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction(new CreateTokenAction);
        $token = $action->execute($user);

        expect($token)->toBeInstanceOf(NewAccessToken::class)
            ->and($token->plainTextToken)->not->toBeEmpty();
    });

});
