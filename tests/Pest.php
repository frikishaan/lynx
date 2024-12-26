<?php

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;

uses(
    Tests\TestCase::class,
    // Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

/**
 * Function to login user
 */
function login()
{
    /** @var App\Models\User */
    $admin = User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
    ]);

    /** @var App\Models\Team */
    $defaultTeam = Team::create([
        'name' => 'Default Team',
        'default_team' => 1
    ]);

    $defaultTeam->members()->attach([
        $admin->id => [ 'role' => 'admin'], 
    ]);

    actingAs($admin);

    Filament::setTenant($defaultTeam);
}

/**
 * Function to create a team
 */
function createTeam() {
    return Team::create([
        'name' => 'Default team'
    ]);
}
