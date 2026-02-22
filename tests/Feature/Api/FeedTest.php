<?php

use App\Models\Player;
use App\Models\Post;

it('returns all posts ordered by most recent', function () {
    $player = Player::factory()->create();
    $oldPost = Post::factory()->create(['player_id' => $player->id, 'created_at' => now()->subMinutes(5)]);
    $newPost = Post::factory()->create(['player_id' => $player->id, 'created_at' => now()]);

    $response = $this->getJson('/api/feed');

    $response->assertSuccessful()
        ->assertJsonCount(2);

    expect($response->json('0.id'))->toBe($newPost->id);
    expect($response->json('1.id'))->toBe($oldPost->id);
});

it('returns correct post structure', function () {
    Post::factory()->create();

    $this->getJson('/api/feed')
        ->assertSuccessful()
        ->assertJsonStructure([
            '*' => ['id', 'author', 'content', 'created_at'],
        ]);
});

it('filters posts by author', function () {
    $jean = Player::factory()->create(['pseudo' => 'jean']);
    $marie = Player::factory()->create(['pseudo' => 'marie']);

    Post::factory()->create(['player_id' => $jean->id]);
    Post::factory()->create(['player_id' => $marie->id]);

    $response = $this->getJson('/api/feed?author=jean');

    $response->assertSuccessful()
        ->assertJsonCount(1);

    expect($response->json('0.author'))->toBe('jean');
});

it('returns empty array when no posts', function () {
    $this->getJson('/api/feed')
        ->assertSuccessful()
        ->assertJson([]);
});
