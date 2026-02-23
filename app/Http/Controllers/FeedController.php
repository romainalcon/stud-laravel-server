<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function __invoke(): View
    {
        $players = Player::query()->orderBy('pseudo')->pluck('pseudo');

        return view('feed', compact('players'));
    }
}
