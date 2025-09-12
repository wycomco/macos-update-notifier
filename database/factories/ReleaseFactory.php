<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Release>
 */
class ReleaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $majorVersions = ['macOS 13', 'macOS 14', 'macOS 15', 'macOS 26'];
        $majorVersion = collect($majorVersions)->random();
        
        return [
            'major_version' => $majorVersion,
            'version' => $this->generateVersion($majorVersion),
            'release_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'raw_json' => $this->generateRawJson($majorVersion),
        ];
    }

    /**
     * Generate a realistic version number
     */
    private function generateVersion(string $majorVersion): string
    {
        $major = str_replace('macOS ', '', $majorVersion);
        $minor = fake()->numberBetween(0, 6);
        $patch = fake()->numberBetween(0, 10);
        
        return "{$major}.{$minor}.{$patch}";
    }

    /**
     * Generate sample raw JSON data
     */
    private function generateRawJson(string $majorVersion): array
    {
        return [
            'ProductVersion' => $this->generateVersion($majorVersion),
            'ReleaseDate' => fake()->dateTimeThisYear()->format('Y-m-d'),
            'ProductName' => $majorVersion,
            'Build' => fake()->randomNumber(5) . strtoupper(fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F'])),
        ];
    }
}
