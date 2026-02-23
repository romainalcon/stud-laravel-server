<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Message;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(SendMessageRequest $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $receiver = Player::query()->where('pseudo', $request->validated('to'))->first();

        if (! $receiver) {
            return response()->json(['message' => 'Joueur introuvable.'], 404);
        }

        if ($player->id === $receiver->id) {
            return response()->json(['message' => 'Tu ne peux pas t\'envoyer un message à toi-même.'], 422);
        }

        $message = Message::query()->create([
            'sender_id' => $player->id,
            'receiver_id' => $receiver->id,
            'content' => $request->validated('content'),
        ]);

        return response()->json([
            'id' => $message->id,
            'from' => $player->pseudo,
            'to' => $receiver->pseudo,
            'content' => $message->content,
            'created_at' => $message->created_at->toIso8601String(),
        ], 201);
    }

    public function inbox(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $messages = $player->receivedMessages()
            ->with('sender')
            ->latest('created_at')
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'from' => $msg->sender->pseudo,
                'content' => $msg->content,
                'read' => $msg->read_at !== null,
                'created_at' => $msg->created_at->toIso8601String(),
            ]);

        return response()->json($messages);
    }

    public function conversation(Request $request, string $pseudo): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $other = Player::query()->where('pseudo', $pseudo)->first();

        if (! $other) {
            return response()->json(['message' => 'Joueur introuvable.'], 404);
        }

        // Marquer les messages reçus comme lus
        Message::query()
            ->where('sender_id', $other->id)
            ->where('receiver_id', $player->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::query()
            ->where(function ($query) use ($player, $other) {
                $query->where('sender_id', $player->id)->where('receiver_id', $other->id);
            })
            ->orWhere(function ($query) use ($player, $other) {
                $query->where('sender_id', $other->id)->where('receiver_id', $player->id);
            })
            ->with(['sender', 'receiver'])
            ->oldest('created_at')
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'from' => $msg->sender->pseudo,
                'to' => $msg->receiver->pseudo,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toIso8601String(),
            ]);

        return response()->json($messages);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', (string) $request->header('Authorization'));
        $player = Player::query()->where('token', $token)->first();

        if (! $player) {
            return response()->json(['message' => 'Token invalide.'], 401);
        }

        $count = $player->receivedMessages()->whereNull('read_at')->count();

        return response()->json(['unread_count' => $count]);
    }
}
