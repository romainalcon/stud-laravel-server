<?php

use App\Models\Follow;
use App\Models\Player;
use App\Models\Post;

it('follows a player', function () {
    $player = Player::factory()->create();
    $target = Player::factory()->create();

    $response = $this->postJson("/api/follow/{$target->pseudo}", [], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'status' => 'following',
            'pseudo' => $target->pseudo,
        ]);

    $this->assertDatabaseHas('follows', [
        'follower_id' => $player->id,
        'followed_id' => $target->id,
    ]);
});

it('unfollows when already following', function () {
    $player = Player::factory()->create();
    $target = Player::factory()->create();

    Follow::query()->create([
        'follower_id' => $player->id,
        'followed_id' => $target->id,
    ]);

    $response = $this->postJson("/api/follow/{$target->pseudo}", [], [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'status' => 'unfollowed',
            'pseudo' => $target->pseudo,
        ]);

    $this->assertDatabaseMissing('follows', [
        'follower_id' => $player->id,
        'followed_id' => $target->id,
    ]);
});

it('cannot follow self', function () {
    $player = Player::factory()->create();

    $this->postJson("/api/follow/{$player->pseudo}", [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertUnprocessable()
        ->assertJson(['message' => 'Tu ne peux pas te suivre toi-même.']);
});

it('lists followers', function () {
    $player = Player::factory()->create();
    $follower1 = Player::factory()->create();
    $follower2 = Player::factory()->create();

    Follow::query()->create(['follower_id' => $follower1->id, 'followed_id' => $player->id]);
    Follow::query()->create(['follower_id' => $follower2->id, 'followed_id' => $player->id]);

    $response = $this->getJson('/api/followers', [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(2);
});

it('lists following', function () {
    $player = Player::factory()->create();
    $target1 = Player::factory()->create();
    $target2 = Player::factory()->create();

    Follow::query()->create(['follower_id' => $player->id, 'followed_id' => $target1->id]);
    Follow::query()->create(['follower_id' => $player->id, 'followed_id' => $target2->id]);

    $response = $this->getJson('/api/following', [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(2);
});

it('returns personal feed with only followed players posts', function () {
    $player = Player::factory()->create();
    $followed = Player::factory()->create();
    $notFollowed = Player::factory()->create();

    Follow::query()->create(['follower_id' => $player->id, 'followed_id' => $followed->id]);

    Post::factory()->create(['player_id' => $followed->id]);
    Post::factory()->create(['player_id' => $notFollowed->id]);

    $response = $this->getJson('/api/feed/personal', [
        'Authorization' => "Bearer {$player->token}",
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(1);

    expect($response->json('0.author'))->toBe($followed->pseudo);
});

it('returns 404 for non-existent player to follow', function () {
    $player = Player::factory()->create();

    $this->postJson('/api/follow/nobody', [], [
        'Authorization' => "Bearer {$player->token}",
    ])->assertNotFound();
});
