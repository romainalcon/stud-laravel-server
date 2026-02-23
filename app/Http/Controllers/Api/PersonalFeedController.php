<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalFeedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $followedIds = $player->following()->pluck('players.id');

        $posts = Post::query()
            ->with('player')
            ->withCount(['likes', 'comments'])
            ->whereIn('player_id', $followedIds)
            ->latest()
            ->get()
            ->map(fn ($post) => [
                'id' => $post->id,
                'author' => $post->player->pseudo,
                'content' => $post->content,
                'tag' => $post->tag,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'created_at' => $post->created_at->toIso8601String(),
            ]);

        return response()->json($posts);
    }
}
