<?php

declare(strict_types=1);

use App\Actions\Auth\CreateTokenAction;
use App\Enums\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

describe('CreateTokenAction', function () {

    it('creates an access token for the user', function () {
        $user = User::factory()->create();

        $action = new CreateTokenAction;
        $token = $action->execute($user);

        expect($token)->toBeInstanceOf(NewAccessToken::class)
            ->and($token->plainTextToken)->not->toBeEmpty();
    });

    it('creates token with correct name', function () {
        $user = User::factory()->create();

        $action = new CreateTokenAction;
        $token = $action->execute($user);

        expect($token->accessToken->name)->toBe(TokenAbility::TOKEN_NAME);
    });

    it('creates token with all abilities', function () {
        $user = User::factory()->create();

        $action = new CreateTokenAction;
        $token = $action->execute($user);

        expect($token->accessToken->abilities)->toBe(TokenAbility::values());
    });

    it('creates token with expiration in the future', function () {
        $user = User::factory()->create();

        $action = new CreateTokenAction;
        $token = $action->execute($user);

        expect($token->accessToken->expires_at)->not->toBeNull()
            ->and($token->accessToken->expires_at->isFuture())->toBeTrue();
    });

});
