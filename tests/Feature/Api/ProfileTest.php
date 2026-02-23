<?php

use App\Models\Like;
use App\Models\Player;
use App\Models\Post;

it('updates profile', function () {
    $player = Player::factory()->create();

    $response = $this->putJson('/api/profile', [
        'bio' => 'Ma super bio',
        'avatar_url' => 'https://example.com/avatar.jpg',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'pseudo' => $player->pseudo,
            'bio' => 'Ma super bio',
            'avatar_url' => 'https://example.com/avatar.jpg',
        ]);

    $this->assertDatabaseHas('players', [
        'id' => $player->id,
        'bio' => 'Ma super bio',
        'avatar_url' => 'https://example.com/avatar.jpg',
    ]);
});

it('rejects bio longer than 280 characters', function () {
    $player = Player::factory()->create();

    $this->putJson('/api/profile', [
        'bio' => str_repeat('a', 281),
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['bio']);
});

it('rejects invalid avatar url', function () {
    $player = Player::factory()->create();

    $this->putJson('/api/profile', [
        'avatar_url' => 'not-a-url',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['avatar_url']);
});

it('lists all profiles', function () {
    Player::factory()->count(3)->create();

    $response = $this->getJson('/api/profiles');

    $response->assertSuccessful()
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => ['pseudo', 'bio', 'avatar_url', 'posts_count', 'likes_received'],
        ]);
});

it('shows a specific profile', function () {
    $player = Player::factory()->create(['pseudo' => 'testuser']);
    $post = Post::factory()->create(['player_id' => $player->id]);
    Like::query()->create(['post_id' => $post->id, 'player_id' => Player::factory()->create()->id]);

    $response = $this->getJson('/api/profiles/testuser');

    $response->assertSuccessful()
        ->assertJson([
            'pseudo' => 'testuser',
            'posts_count' => 1,
            'likes_received' => 1,
        ])
        ->assertJsonStructure(['pseudo', 'bio', 'avatar_url', 'posts_count', 'likes_received', 'created_at']);
});

it('returns 404 for non-existent profile', function () {
    $this->getJson('/api/profiles/nobody')
        ->assertNotFound()
        ->assertJson(['message' => 'Joueur introuvable.']);
});

it('rejects profile update without token', function () {
    $this->putJson('/api/profile', ['bio' => 'test'])
        ->assertUnauthorized();
});
