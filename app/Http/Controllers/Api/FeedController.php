<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $posts = Post::query()
            ->with('player')
            ->when($request->query('author'), function ($query, $author) {
                $query->whereHas('player', fn ($q) => $q->where('pseudo', $author));
            })
            ->latest()
            ->get()
            ->map(fn ($post) => [
                'id' => $post->id,
                'author' => $post->player->pseudo,
                'content' => $post->content,
                'created_at' => $post->created_at->toIso8601String(),
            ]);

        return response()->json($posts);
    }
}
