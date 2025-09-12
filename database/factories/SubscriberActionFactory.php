<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriberAction>
 */
class SubscriberActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['subscribed', 'unsubscribed', 'version_changed', 'notification_sent'];
        $action = $this->faker->randomElement($actions);
        
        $details = [];
        if ($action === 'version_changed') {
            $details = [
                'old_version' => 'macOS 14',
                'new_version' => 'macOS 15'
            ];
        } elseif ($action === 'notification_sent') {
            $details = [
                'release_id' => 1,
                'version' => '14.1',
                'major_version' => 'macOS 14'
            ];
        }
        
        return [
            'subscriber_id' => Subscriber::factory(),
            'action' => $action,
            'data' => $details,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
