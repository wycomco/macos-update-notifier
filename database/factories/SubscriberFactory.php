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
        $macosVersions = ['Sonoma', 'Monterey', 'Big Sur', 'Ventura'];
        $numVersions = fake()->numberBetween(1, 2);
        $selectedVersions = collect($versions)->random($numVersions)->values()->toArray();
        
        return [
            'email' => fake()->unique()->safeEmail(),
            'macos_version' => collect($macosVersions)->random(),
            'subscribed_versions' => $selectedVersions,
            'days_to_install' => fake()->numberBetween(7, 60),
            'is_subscribed' => true,
            'admin_id' => \App\Models\User::factory(),
        ];
    }
}
