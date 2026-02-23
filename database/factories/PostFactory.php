<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_id' => Player::factory(),
            'content' => fake()->sentence(),
            'tag' => null,
        ];
    }

    public function withTag(?string $tag = null): static
    {
        return $this->state(fn (array $attributes) => [
            'tag' => $tag ?? fake()->randomElement(['humeur', 'question', 'annonce', 'blague', 'code', 'random']),
        ]);
    }
}
