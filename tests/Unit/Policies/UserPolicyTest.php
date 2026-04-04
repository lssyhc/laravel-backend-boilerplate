<?php

declare(strict_types=1);

use App\Models\User;

/*
|--------------------------------------------------------------------------
| UserPolicy
|--------------------------------------------------------------------------
*/

describe('UserPolicy', function () {

    it('allows a user to view their own profile', function () {
        $user = User::factory()->create();

        expect($user->can('view', $user))->toBeTrue();
    });

    it('denies a user from viewing another user profile', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        expect($user->can('view', $otherUser))->toBeFalse();
    });

    it('allows a user to update their own profile', function () {
        $user = User::factory()->create();

        expect($user->can('update', $user))->toBeTrue();
    });

    it('denies a user from updating another user profile', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        expect($user->can('update', $otherUser))->toBeFalse();
    });

    it('allows a user to delete their own profile', function () {
        $user = User::factory()->create();

        expect($user->can('delete', $user))->toBeTrue();
    });

    it('denies a user from deleting another user profile', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        expect($user->can('delete', $otherUser))->toBeFalse();
    });

});
