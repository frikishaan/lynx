<?php

use App\Models\Membership;
use App\Models\Team;
use App\Models\User;

it('can create an account using command', function () {
    $this->artisan('lynx:create-account')
        ->expectsQuestion('Enter your name', 'Test User')
        ->expectsQuestion('Enter an email address', 'test@example.com')
        ->expectsQuestion('Enter password', 'secret')
        ->expectsQuestion('Enter your team name', 'Test team')
        ->assertSuccessful()
        ->expectsOutputToContain('Account created successfully!');

    $this->assertDatabaseHas(Team::class, [
        'name' => 'Test team'
    ]);
    
    $this->assertDatabaseHas(User::class, [
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $this->assertDatabaseHas(Membership::class, [
        'team_id' => 1,
        'user_id' => 1,
        'role' => 'admin'
    ]);
});