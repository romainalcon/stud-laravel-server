<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory;

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Message $message) {
            $message->created_at = $message->freshTimestamp();
        });
    }

    /**
     * @return BelongsTo<Player, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'sender_id');
    }

    /**
     * @return BelongsTo<Player, $this>
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'receiver_id');
    }
}
