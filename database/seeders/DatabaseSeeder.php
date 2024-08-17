<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        Link::create([
            'long_url' => 'https://filamentphp.com/docs/3.x/forms/fields/repeater',
            'title' => 'Repeater - Form Builder - Filament'
        ]);

        // with password
        Link::create([
            'long_url' => 'https://filamentphp.com/docs/3.x/support/blade-components/loading-indicator',
            'title' => 'Loading indicator Blade component - Core Concepts - Filament',
            'password' => Hash::make('password')
        ]);
    }
}
