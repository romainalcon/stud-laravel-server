<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggle(Request $request, string $pseudo): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $target = Player::query()->where('pseudo', $pseudo)->first();

        if (! $target) {
            return response()->json(['message' => 'Joueur introuvable.'], 404);
        }

        if ($player->id === $target->id) {
            return response()->json(['message' => 'Tu ne peux pas te suivre toi-même.'], 422);
        }

        $follow = Follow::query()
            ->where('follower_id', $player->id)
            ->where('followed_id', $target->id)
            ->first();

        if ($follow) {
            $follow->delete();

            return response()->json([
                'status' => 'unfollowed',
                'pseudo' => $target->pseudo,
            ]);
        }

        Follow::query()->create([
            'follower_id' => $player->id,
            'followed_id' => $target->id,
        ]);

        return response()->json([
            'status' => 'following',
            'pseudo' => $target->pseudo,
        ]);
    }

    public function followers(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $followers = $player->followers()->get()->map(fn ($p) => ['pseudo' => $p->pseudo]);

        return response()->json($followers);
    }

    public function following(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $following = $player->following()->get()->map(fn ($p) => ['pseudo' => $p->pseudo]);

        return response()->json($following);
    }
}
