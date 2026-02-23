<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Player;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __invoke(Request $request, int $id): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $post = Post::query()->find($id);

        if (! $post) {
            return response()->json(['message' => 'Post introuvable.'], 404);
        }

        $like = Like::query()
            ->where('post_id', $post->id)
            ->where('player_id', $player->id)
            ->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            Like::query()->create([
                'post_id' => $post->id,
                'player_id' => $player->id,
            ]);
            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
