<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $player = Player::query()->create([
            'pseudo' => $request->validated('pseudo'),
            'port' => $request->validated('port'),
            'token' => Str::random(64),
        ]);

        return response()->json([
            'token' => $player->token,
            'message' => "Bienvenue {$player->pseudo} !",
        ], 201);
    }
}
