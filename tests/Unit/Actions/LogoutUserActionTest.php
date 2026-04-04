<?php

use App\Actions\Auth\LogoutUserAction;
use App\Models\User;

describe('LogoutUserAction', function () {

    it('deletes the current access token', function () {
        $user = User::factory()->create();
        $token = $user->createToken('auth');

        // Simulate the user having the current token
        $user->withAccessToken($token->accessToken);

        $action = new LogoutUserAction;
        $action->execute($user);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    });

});
