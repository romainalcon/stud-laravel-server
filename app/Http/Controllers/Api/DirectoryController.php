<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class DirectoryController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $players = Player::query()
            ->select('pseudo', 'port')
            ->get();

        return response()->json($players);
    }
}
