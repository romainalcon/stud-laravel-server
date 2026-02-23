<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pseudo' => fake()->unique()->userName(),
            'port' => fake()->unique()->numberBetween(8000, 8099),
            'token' => Str::random(64),
            'bio' => null,
            'avatar_url' => null,
        ];
    }

    public function withProfile(): static
    {
        return $this->state(fn (array $attributes) => [
            'bio' => fake()->sentence(),
            'avatar_url' => fake()->imageUrl(),
        ]);
    }
}
