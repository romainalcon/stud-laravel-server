<?php

use App\Models\Player;

it('registers a new player and returns a token', function () {
    $response = $this->postJson('/api/register', [
        'pseudo' => 'jean',
        'port' => 8042,
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['token', 'message'])
        ->assertJson(['message' => 'Bienvenue jean !']);

    expect($response->json('token'))->toHaveLength(64);

    $this->assertDatabaseHas('players', [
        'pseudo' => 'jean',
        'port' => 8042,
    ]);
});

it('rejects duplicate pseudo', function () {
    Player::factory()->create(['pseudo' => 'jean']);

    $this->postJson('/api/register', [
        'pseudo' => 'jean',
        'port' => 8050,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['pseudo']);
});

it('requires pseudo and port', function () {
    $this->postJson('/api/register', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['pseudo', 'port']);
});

it('rejects pseudo longer than 30 characters', function () {
    $this->postJson('/api/register', [
        'pseudo' => str_repeat('a', 31),
        'port' => 8000,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['pseudo']);
});

it('rejects port outside valid range', function (int $port) {
    $this->postJson('/api/register', [
        'pseudo' => 'test',
        'port' => $port,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['port']);
})->with([7999, 8100, 0, 9999]);
