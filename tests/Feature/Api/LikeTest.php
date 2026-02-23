<?php

use App\Models\Like;
use App\Models\Player;
use App\Models\Post;

it('likes a post', function () {
    $player = Player::factory()->create();
    $post = Post::factory()->create();

    $response = $this->postJson("/api/posts/{$post->id}/like", [], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'status' => 'liked',
            'likes_count' => 1,
        ]);

    $this->assertDatabaseHas('likes', [
        'post_id' => $post->id,
        'player_id' => $player->id,
    ]);
});

it('unlikes a post when already liked', function () {
    $player = Player::factory()->create();
    $post = Post::factory()->create();

    Like::query()->create([
        'post_id' => $post->id,
        'player_id' => $player->id,
    ]);

    $response = $this->postJson("/api/posts/{$post->id}/like", [], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'status' => 'unliked',
            'likes_count' => 0,
        ]);

    $this->assertDatabaseMissing('likes', [
        'post_id' => $post->id,
        'player_id' => $player->id,
    ]);
});

it('returns 404 for non-existent post', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/posts/9999/like', [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertNotFound()
        ->assertJson(['message' => 'Post introuvable.']);
});

it('rejects like without token', function () {
    $post = Post::factory()->create();

    $this->postJson("/api/posts/{$post->id}/like")
        ->assertUnauthorized();
});

it('includes likes_count in feed', function () {
    $post = Post::factory()->create();
    Like::query()->create([
        'post_id' => $post->id,
        'player_id' => Player::factory()->create()->id,
    ]);

    $response = $this->getJson('/api/feed');

    $response->assertSuccessful();
    expect($response->json('0.likes_count'))->toBe(1);
});
