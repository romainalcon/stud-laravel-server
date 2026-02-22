<?php

use App\Models\Player;

it('returns all registered players', function () {
    Player::factory()->create(['pseudo' => 'jean', 'port' => 8042]);
    Player::factory()->create(['pseudo' => 'marie', 'port' => 8015]);

    $response = $this->getJson('/api/directory');

    $response->assertSuccessful()
        ->assertJsonCount(2)
        ->assertJsonStructure([
            '*' => ['pseudo', 'port'],
        ]);
});

it('does not expose tokens', function () {
    Player::factory()->create();

    $response = $this->getJson('/api/directory');

    $response->assertSuccessful();
    expect($response->json('0'))->not->toHaveKey('token');
    expect($response->json('0'))->not->toHaveKey('id');
});

it('returns empty array when no players', function () {
    $this->getJson('/api/directory')
        ->assertSuccessful()
        ->assertJson([]);
});
