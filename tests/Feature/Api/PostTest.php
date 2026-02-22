<?php

use App\Models\Player;

it('creates a post with valid token', function () {
    $player = Player::factory()->create();

    $response = $this->postJson('/api/posts', [
        'content' => 'Mon premier post !',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertCreated()
        ->assertJson([
            'author' => $player->pseudo,
            'content' => 'Mon premier post !',
        ])
        ->assertJsonStructure(['id', 'author', 'content', 'created_at']);

    $this->assertDatabaseHas('posts', [
        'player_id' => $player->id,
        'content' => 'Mon premier post !',
    ]);
});

it('rejects request without token', function () {
    $this->postJson('/api/posts', [
        'content' => 'Test',
    ])->assertUnauthorized()
        ->assertJson(['message' => 'Token invalide.']);
});

it('rejects request with invalid token', function () {
    $this->postJson('/api/posts', [
        'content' => 'Test',
    ], [
        'Authorization' => 'Bearer invalid-token',
    ])->assertUnauthorized()
        ->assertJson(['message' => 'Token invalide.']);
});

it('rejects post without content', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/posts', [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

it('rejects content longer than 500 characters', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/posts', [
        'content' => str_repeat('a', 501),
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});
