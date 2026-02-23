<?php

use App\Models\Player;
use App\Models\Post;

it('creates a post with a valid tag', function () {
    $player = Player::factory()->create();

    $response = $this->postJson('/api/posts', [
        'content' => 'Test avec tag',
        'tag' => 'question',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertCreated()
        ->assertJson([
            'tag' => 'question',
        ]);

    $this->assertDatabaseHas('posts', [
        'player_id' => $player->id,
        'tag' => 'question',
    ]);
});

it('creates a post without a tag', function () {
    $player = Player::factory()->create();

    $response = $this->postJson('/api/posts', [
        'content' => 'Test sans tag',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertCreated()
        ->assertJson([
            'tag' => null,
        ]);
});

it('rejects an invalid tag', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/posts', [
        'content' => 'Test',
        'tag' => 'invalid',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['tag']);
});

it('filters feed by tag', function () {
    $player = Player::factory()->create();

    Post::factory()->create(['player_id' => $player->id, 'tag' => 'question']);
    Post::factory()->create(['player_id' => $player->id, 'tag' => 'blague']);
    Post::factory()->create(['player_id' => $player->id, 'tag' => null]);

    $response = $this->getJson('/api/feed?tag=question');

    $response->assertSuccessful()
        ->assertJsonCount(1);

    expect($response->json('0.tag'))->toBe('question');
});

it('includes tag in feed response', function () {
    Post::factory()->create(['tag' => 'code']);

    $response = $this->getJson('/api/feed');

    $response->assertSuccessful()
        ->assertJsonStructure([
            '*' => ['id', 'author', 'content', 'tag', 'likes_count', 'comments_count', 'created_at'],
        ]);
});
