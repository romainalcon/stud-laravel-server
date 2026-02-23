<?php

use App\Models\Comment;
use App\Models\Player;
use App\Models\Post;

it('creates a comment on a post', function () {
    $player = Player::factory()->create();
    $post = Post::factory()->create();

    $response = $this->postJson("/api/posts/{$post->id}/comments", [
        'content' => 'Super post !',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertCreated()
        ->assertJson([
            'post_id' => $post->id,
            'author' => $player->pseudo,
            'content' => 'Super post !',
        ]);

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'player_id' => $player->id,
        'content' => 'Super post !',
    ]);
});

it('lists comments on a post', function () {
    $post = Post::factory()->create();
    $player = Player::factory()->create();

    Comment::factory()->create(['post_id' => $post->id, 'player_id' => $player->id, 'created_at' => now()->subMinute()]);
    Comment::factory()->create(['post_id' => $post->id, 'player_id' => $player->id, 'created_at' => now()]);

    $response = $this->getJson("/api/posts/{$post->id}/comments");

    $response->assertSuccessful()
        ->assertJsonCount(2)
        ->assertJsonStructure([
            '*' => ['id', 'author', 'content', 'created_at'],
        ]);
});

it('deletes own comment', function () {
    $player = Player::factory()->create();
    $comment = Comment::factory()->create(['player_id' => $player->id]);

    $this->deleteJson("/api/comments/{$comment->id}", [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertNoContent();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('cannot delete someone else comment', function () {
    $player = Player::factory()->create();
    $other = Player::factory()->create();
    $comment = Comment::factory()->create(['player_id' => $other->id]);

    $this->deleteJson("/api/comments/{$comment->id}", [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertForbidden()
        ->assertJson(['message' => 'Tu ne peux supprimer que tes propres commentaires.']);
});

it('rejects comment without content', function () {
    $player = Player::factory()->create();
    $post = Post::factory()->create();

    $this->postJson("/api/posts/{$post->id}/comments", [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

it('rejects comment longer than 280 characters', function () {
    $player = Player::factory()->create();
    $post = Post::factory()->create();

    $this->postJson("/api/posts/{$post->id}/comments", [
        'content' => str_repeat('a', 281),
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

it('returns 404 for comment on non-existent post', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/posts/9999/comments', [
        'content' => 'test',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertNotFound();
});

it('includes comments_count in feed', function () {
    $post = Post::factory()->create();
    Comment::factory()->count(3)->create(['post_id' => $post->id]);

    $response = $this->getJson('/api/feed');

    $response->assertSuccessful();
    expect($response->json('0.comments_count'))->toBe(3);
});
