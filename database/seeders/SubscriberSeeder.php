<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin if none exists
        $admin = \App\Models\User::first();
        if (!$admin) {
            $admin = \App\Models\User::create([
                'name' => 'Default Admin',
                'email' => 'admin@default.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_super_admin' => true,
            ]);
        }

        // Create test subscribers
        Subscriber::create([
            'email' => 'admin@example.com',
            'subscribed_versions' => ['macOS 14', 'macOS 15'],
            'days_to_install' => 30,
            'admin_id' => $admin->id,
        ]);

        Subscriber::create([
            'email' => 'user1@example.com',
            'subscribed_versions' => ['macOS 14'],
            'days_to_install' => 14,
            'admin_id' => $admin->id,
        ]);

        Subscriber::create([
            'email' => 'user2@example.com',
            'subscribed_versions' => ['macOS 15'],
            'days_to_install' => 7,
            'admin_id' => $admin->id,
        ]);

        Subscriber::create([
            'email' => 'urgent@example.com',
            'subscribed_versions' => ['macOS 14', 'macOS 15'],
            'days_to_install' => 3, // Short deadline for testing
            'admin_id' => $admin->id,
        ]);
    }
}
