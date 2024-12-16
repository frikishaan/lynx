<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\spin;

class CreateAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lynx:create-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use this command to create a Admin user for the Lynx app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        intro('Please provide the below details to create the user');

        $responses = form()
            ->text(
                label: 'Enter your name',
                required: 'Name is required field',
                name: 'name'
            )
            ->text(
                label: 'Enter an email address',
                required: 'Email address is a required field',
                validate: ['email' => 'email|unique:users'],
                name: 'email'
            )
            ->password(
                label: 'Enter password',
                required: 'Password is a required field',
                name: 'password'
            )
            ->text(
                label: 'Enter your team name',
                placeholder: 'Marketing team',
                default: 'Default team',
                name: 'team_name'
            )
            ->submit();

        spin(
            message: 'Creating account...',
            callback: function () use($responses) {

                $user = User::create([
                    'name' => $responses['name'],
                    'email' => $responses['email'],
                    'password' => $responses['password']
                ]);
                
                /** @var Team */
                $team = Team::create([
                    'name' => $responses['team_name'],
                    'default_team' => 1
                ]);

                $team->members()->attach([
                    $user->id => [ 'role' => 'admin'],
                ]);

                clear();

                info('Account created successfully!');
            }
        );
    }
}
