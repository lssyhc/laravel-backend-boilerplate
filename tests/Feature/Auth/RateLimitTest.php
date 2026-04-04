<?php

declare(strict_types=1);

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Rate Limiting
|--------------------------------------------------------------------------
*/

describe('Rate Limiting', function () {

    it('throttles login after 5 attempts', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'john@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    });

    it('throttles register after 5 attempts', function () {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/register', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        }

        $this->postJson('/api/auth/register', [
            'name' => 'User Extra',
            'email' => 'extra@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(429);
    });

});
