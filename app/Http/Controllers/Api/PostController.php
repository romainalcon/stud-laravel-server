<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __invoke(StorePostRequest $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $post = $player->posts()->create([
            'content' => $request->validated('content'),
            'tag' => $request->validated('tag'),
        ]);

        return response()->json([
            'id' => $post->id,
            'author' => $player->pseudo,
            'content' => $post->content,
            'tag' => $post->tag,
            'created_at' => $post->created_at->toIso8601String(),
        ], 201);
    }
}
