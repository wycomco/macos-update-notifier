<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $versions = ['macOS 14', 'macOS 15'];
        $numVersions = fake()->numberBetween(1, 2);
        $selectedVersions = collect($versions)->random($numVersions)->values()->toArray();
        
        return [
            'email' => fake()->unique()->safeEmail(),
            'subscribed_versions' => $selectedVersions,
            'days_to_install' => fake()->numberBetween(7, 60),
            'is_subscribed' => true,
            'language' => config('subscriber_languages.default', 'en'),
            'admin_id' => \App\Models\User::factory(),
        ];
    }
}
