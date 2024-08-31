<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\LinkChoice;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // with UTM
        Link::create([
            'long_url' => 'https://filamentphp.com/docs/3.x/forms/fields/repeater',
            'title' => 'Repeater - Form Builder - Filament',
            'has_utm_params' => 1,
            'utm_source' => 'marketing',
            'utm_medium' => 'marketing-website',
            'utm_campaign' => 'marketing-01',
            'utm_term' => '',
            'utm_content' => 'referral'
        ])
        ->visits()
        ->saveMany([
            new Visit([
                'ip' => '137.105.96.102',
                'country' => 'India'
            ]),
            new Visit([
                'ip' => '142.23.41.101',
                'country' => 'USA'
            ])
        ]);

        // with password
        Link::create([
            'long_url' => 'https://filamentphp.com/docs/3.x/support/blade-components/loading-indicator',
            'title' => 'Loading indicator Blade component - Core Concepts - Filament',
            'password' => Hash::make('password')
        ]);

        // With choices
        Link::create([
            'long_url' => 'https://www.amazon.in/Wealth-Nations-Adam-Smith/dp/9387779467/',
            'title' => 'The wealth of nations by Adam Smith',
            'choice_page_title' => 'Buy The wealth of nations by Adam Smith',
            'choice_page_description' => 'Discover the groundbreaking ideas and principles of modern economics in The Wealth of Nations by Adam Smith. This seminal work examines the wealth creation process, the division of labor, and the market forces that shape economies, laying the foundation for the study of capitalism.',
            'enable_dark_mode' => 1
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
    }
}
