<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    /** @use HasFactory<\Database\Factories\PlayerFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'pseudo',
        'port',
        'token',
        'bio',
        'avatar_url',
    ];

    /** @var list<string> */
    protected $hidden = [
        'token',
    ];

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return HasMany<Like, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany<Player, $this>
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'follows', 'followed_id', 'follower_id');
    }

    /**
     * @return BelongsToMany<Player, $this>
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'follows', 'follower_id', 'followed_id');
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
