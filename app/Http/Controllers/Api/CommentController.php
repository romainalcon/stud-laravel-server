<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Player;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, int $id): JsonResponse
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

        $comment = Comment::query()->create([
            'post_id' => $post->id,
            'player_id' => $player->id,
            'content' => $request->validated('content'),
        ]);

        return response()->json([
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'author' => $player->pseudo,
            'content' => $comment->content,
            'created_at' => $comment->created_at->toIso8601String(),
        ], 201);
    }

    public function index(int $id): JsonResponse
    {
        $post = Post::query()->find($id);

        if (! $post) {
            return response()->json(['message' => 'Post introuvable.'], 404);
        }

        $comments = $post->comments()
            ->with('player')
            ->oldest()
            ->get()
            ->map(fn ($comment) => [
                'id' => $comment->id,
                'author' => $comment->player->pseudo,
                'content' => $comment->content,
                'created_at' => $comment->created_at->toIso8601String(),
            ]);

        return response()->json($comments);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $comment = Comment::query()->find($id);

        if (! $comment) {
            return response()->json(['message' => 'Commentaire introuvable.'], 404);
        }

        if ($comment->player_id !== $player->id) {
            return response()->json(['message' => 'Tu ne peux supprimer que tes propres commentaires.'], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}
