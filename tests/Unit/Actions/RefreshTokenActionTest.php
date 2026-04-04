<?php

declare(strict_types=1);

use App\Actions\Auth\RefreshTokenAction;
use App\Enums\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

describe('RefreshTokenAction', function () {

    it('creates a new access token for the user', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction;
        $token = $action->execute($user);

        expect($token)->toBeInstanceOf(NewAccessToken::class)
            ->and($token->plainTextToken)->not->toBeEmpty();
    });

    it('creates token with correct name', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction;
        $token = $action->execute($user);

        expect($token->accessToken->name)->toBe(TokenAbility::TOKEN_NAME);
    });

    it('creates token with expiration', function () {
        $user = User::factory()->create();

        $action = new RefreshTokenAction;
        $token = $action->execute($user);

        expect($token->accessToken->expires_at)->not->toBeNull();
    });

});
