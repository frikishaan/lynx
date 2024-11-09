<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip' => fake()->ipv4(),
            'country' => fake()->randomElement([
                'India', 'USA', 'England', 'Spain', 'Mexico', 'Japan', 'Australia',
                'Ireland', 'Portugal', 'UAE', 'New Zealand', 'Qatar', 'Nigeria'
            ]),
            'browser' => fake()->randomElement([
                'Chrome', 'Microsoft Edge', 'Opera', 'Safari'
            ]),
            'device' => fake()->randomElement([
                'Desktop', 'Smartphone', 'Tablet'
            ]),
            'os' => fake()->randomElement([
                'Windows', 'Android', 'iOS', 'Mac'
            ]),
            'visited_at' => fake()->dateTimeBetween('-13 months', 'now'),
            'link_id' => random_int(1, 4)
        ];
    }
}
