<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    /** @use HasFactory<\Database\Factories\FollowFactory> */
    use HasFactory;

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Follow $follow) {
            $follow->created_at = $follow->freshTimestamp();
        });
    }

    /**
     * @return BelongsTo<Player, $this>
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'follower_id');
    }

    /**
     * @return BelongsTo<Player, $this>
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'followed_id');
    }
}
