<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Like;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $player->update($request->validated());

        return response()->json([
            'pseudo' => $player->pseudo,
            'port' => $player->port,
            'bio' => $player->bio,
            'avatar_url' => $player->avatar_url,
            'posts_count' => $player->posts()->count(),
            'likes_received' => Like::query()->whereIn('post_id', $player->posts()->pluck('id'))->count(),
            'created_at' => $player->created_at->toIso8601String(),
        ]);
    }

    public function index(): JsonResponse
    {
        $players = Player::query()
            ->withCount('posts')
            ->get()
            ->map(function ($player) {
                return [
                    'pseudo' => $player->pseudo,
                    'bio' => $player->bio,
                    'avatar_url' => $player->avatar_url,
                    'posts_count' => $player->posts_count,
                    'likes_received' => Like::query()->whereIn('post_id', $player->posts()->pluck('id'))->count(),
                    'followers_count' => $player->followers()->count(),
                    'following_count' => $player->following()->count(),
                ];
            });

        return response()->json($players);
    }

    public function show(string $pseudo): JsonResponse
    {
        $player = Player::query()->where('pseudo', $pseudo)->first();

        if (! $player) {
            return response()->json(['message' => 'Joueur introuvable.'], 404);
        }

        return response()->json([
            'pseudo' => $player->pseudo,
            'bio' => $player->bio,
            'avatar_url' => $player->avatar_url,
            'posts_count' => $player->posts()->count(),
            'likes_received' => Like::query()->whereIn('post_id', $player->posts()->pluck('id'))->count(),
            'followers_count' => $player->followers()->count(),
            'following_count' => $player->following()->count(),
            'created_at' => $player->created_at->toIso8601String(),
        ]);
    }
}
