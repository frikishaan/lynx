<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkChoice;
use App\Models\Team;
use App\Models\User;
use App\Models\Visit;
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
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        $user = User::factory()->create([
            'name' => 'Normal user',
            'email' => 'user@example.com'
        ]);

        // default team
        /** @var Team */
        $defaultTeam = Team::create([
            'name' => "Default Team",
            'user_id' => $admin->id,
        ]);

        $defaultTeam->members()->attach([
            $admin->id => [ 'role' => 'admin'], 
            $user->id => [ 'role' => 'user' ]
        ]);

        $defaultTeam->domains()->saveMany([
            new Domain([
                'name' => 'lynx1.test',
                'created_at' => now(),
                'updated_at' => now()
            ]),
            new Domain([
                'name' => 'lynx2.test',
                'created_at' => now(),
                'updated_at' => now()
            ])
        ]);

        // with UTM
        $link = Link::create([
            'long_url' => 'https://filamentphp.com/docs/3.x/forms/fields/repeater',
            'title' => 'Repeater - Form Builder - Filament',
            'has_utm_params' => 1,
            'utm_source' => 'marketing',
            'utm_medium' => 'marketing-website',
            'utm_campaign' => 'marketing-01',
            'utm_term' => '',
            'utm_content' => 'referral',
            'team_id' => $defaultTeam->id
        ]);

        // with password
        $link2 = Link::create([
            'long_url' => 'https://filamentphp.com/docs/3.x/support/blade-components/loading-indicator',
            'title' => 'Loading indicator Blade component - Core Concepts - Filament',
            'password' => Hash::make('password'),
            'team_id' => $defaultTeam->id
        ]);

        // With choices
        $link3 = Link::create([
            'long_url' => 'https://www.amazon.in/Wealth-Nations-Adam-Smith/dp/9387779467/',
            'title' => 'The wealth of nations by Adam Smith',
            'choice_page_title' => 'Buy The wealth of nations by Adam Smith',
            'choice_page_description' => 'Discover the groundbreaking ideas and principles of modern economics in The Wealth of Nations by Adam Smith. This seminal work examines the wealth creation process, the division of labor, and the market forces that shape economies, laying the foundation for the study of capitalism.',
            'enable_dark_mode' => 1,
            'team_id' => $defaultTeam->id
        ])
        ->choices()
        ->saveMany([
            new LinkChoice([
                'destination_url' => 'https://www.amazon.in/Wealth-Nations-Adam-Smith/dp/9387779467/',
                'title' => 'Amazon India',
                'description' => 'For Indian users, buy from Amazon\'s India store.',
                'sort_order' => 2
            ]),
            new LinkChoice([
                'destination_url' => 'https://www.amazon.com/Wealth-Nations-Adam-Smith/dp/9387779467/',
                'title' => 'Amazon.com',
                'description' => 'For any other users, buy from Amazon\'s official store.',
                'sort_order' => 1
            ]),
            new LinkChoice([
                'destination_url' =>
                    'https://www.amazon.co.uk/Wealth-Nations-Adam-Smith/dp/9387779467/',
                'title' => 'Amazon UK',
                'description' => 'For residents of UK, buy from Amazon\'s UK store.',
                'sort_order' => 3
            ])
        ]);

        // Expired link
        Link::create([
            'long_url' => 'https://laravel.com/docs/11.x/',
            'title' => 'Expired Link',
            'team_id' => $defaultTeam->id,
            'expires_at' => now()->subHour()
        ]);

        /** @var Team */
        $marketingTeam = Team::create([
            'name' => 'Marketing team',
            'user_id' => $admin->id
        ]);

        $user2 = User::factory()->create();

        $marketingTeam->members()->attach([
            $admin->id => [ 'role' => 'admin'], 
            $user->id => [ 'role' => 'user' ],
            $user2->id => [ 'role' => 'user' ]
        ]);

        $marketingTeam->domains()->saveMany([
            new Domain([
                'name' => 'lynx3.test',
                'created_at' => now(),
                'updated_at' => now()
            ]),
            new Domain([
                'name' => 'lynx4.test',
                'created_at' => now(),
                'updated_at' => now()
            ])
        ]);

        Link::create([
            'long_url' => 'https://laravel.com/docs/11.x/helpers',
            'title' => 'Helpers - Laravel',
            'team_id' => $marketingTeam->id
        ]);

        // Visits
        $visits = Visit::factory(26784)->make()->toArray();
        
        foreach (array_chunk($visits, 1000) as $chunk) {
            Visit::insert($chunk);
        }
    }
}
