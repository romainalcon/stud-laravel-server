<?php

use App\Models\Message;
use App\Models\Player;

it('sends a message', function () {
    $sender = Player::factory()->create();
    $receiver = Player::factory()->create();

    $response = $this->postJson('/api/messages', [
        'to' => $receiver->pseudo,
        'content' => 'Salut !',
    ], [
        'Authorization' => "Bearer {$sender->token}",
    ]);

    $response->assertCreated()
        ->assertJson([
            'from' => $sender->pseudo,
            'to' => $receiver->pseudo,
            'content' => 'Salut !',
        ]);

    $this->assertDatabaseHas('messages', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'Salut !',
    ]);
});

it('rejects message to non-existent player', function () {
    $sender = Player::factory()->create();

    $this->postJson('/api/messages', [
        'to' => 'nobody',
        'content' => 'Salut !',
    ], [
        'Authorization' => "Bearer {$sender->token}",
    ])->assertUnprocessable();
});

it('rejects message to self', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/messages', [
        'to' => $player->pseudo,
        'content' => 'Moi-même',
    ], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable();
});

it('lists inbox messages', function () {
    $player = Player::factory()->create();
    $other = Player::factory()->create();

    Message::query()->create([
        'sender_id' => $other->id,
        'receiver_id' => $player->id,
        'content' => 'Hello',
    ]);

    $response = $this->getJson('/api/messages', [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(1)
        ->assertJsonStructure([
            '*' => ['id', 'from', 'content', 'read', 'created_at'],
        ]);

    expect($response->json('0.read'))->toBeFalse();
});

it('shows conversation and marks as read', function () {
    $player = Player::factory()->create();
    $other = Player::factory()->create();

    Message::query()->create([
        'sender_id' => $other->id,
        'receiver_id' => $player->id,
        'content' => 'Salut',
    ]);

    Message::query()->create([
        'sender_id' => $player->id,
        'receiver_id' => $other->id,
        'content' => 'Hello',
    ]);

    $response = $this->getJson("/api/messages/{$other->pseudo}", [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(2)
        ->assertJsonStructure([
            '*' => ['id', 'from', 'to', 'content', 'created_at'],
        ]);

    // Vérifie que les messages reçus sont marqués comme lus
    $this->assertDatabaseMissing('messages', [
        'sender_id' => $other->id,
        'receiver_id' => $player->id,
        'read_at' => null,
    ]);
});

it('returns unread count', function () {
    $player = Player::factory()->create();
    $other = Player::factory()->create();

    Message::query()->create([
        'sender_id' => $other->id,
        'receiver_id' => $player->id,
        'content' => 'Message 1',
    ]);

    Message::query()->create([
        'sender_id' => $other->id,
        'receiver_id' => $player->id,
        'content' => 'Message 2',
    ]);

    $response = $this->getJson('/api/messages/unread/count', [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJson(['unread_count' => 2]);
});

it('rejects message without content', function () {
    $sender = Player::factory()->create();
    $receiver = Player::factory()->create();

    $this->postJson('/api/messages', [
        'to' => $receiver->pseudo,
    ], [
        'Authorization' => "Bearer {$sender->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

it('rejects message longer than 500 characters', function () {
    $sender = Player::factory()->create();
    $receiver = Player::factory()->create();

    $this->postJson('/api/messages', [
        'to' => $receiver->pseudo,
        'content' => str_repeat('a', 501),
    ], [
        'Authorization' => "Bearer {$sender->token}",
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});
