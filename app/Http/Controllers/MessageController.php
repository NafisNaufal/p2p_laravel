<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\UserPin;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('sender_session_id', session('user_session_id'))
            ->orWhere('receiver_session_id', session('user_session_id'))
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_session_id' => 'required|string',
            'message' => 'required|string|max:1000'
        ]);

        // Verify receiver exists
        $receiver = UserPin::where('session_id', $request->receiver_session_id)
            ->where('is_active', true)
            ->first();

        if (!$receiver) {
            return response()->json(['error' => 'Peer not found or inactive'], 404);
        }

        $message = Message::create([
            'sender_session_id' => session('user_session_id'),
            'receiver_session_id' => $request->receiver_session_id,
            'message' => $request->message,
            'message_type' => 'text',
            'sender_ip' => $request->ip(),
            'receiver_ip' => $receiver->ip_address
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'network_info' => [
                'protocol' => 'TCP',
                'method' => 'POST',
                'content_type' => 'application/json',
                'sender_ip' => $request->ip(),
                'receiver_ip' => $receiver->ip_address
            ]
        ]);
    }

    public function fetch(Request $request)
    {
        $peerSessionId = $request->query('peer_session_id');

        if (!$peerSessionId) {
            return response()->json(['messages' => []]);
        }

        $currentUserSessionId = session('user_session_id');

        $currentUserSessionId = (string) $currentUserSessionId;
        $peerSessionId = (string) $peerSessionId;

        $messages = Message::where(function ($query) use ($peerSessionId, $currentUserSessionId) {
            // Get all messages between current user and peer
            $query->where(function ($q) use ($currentUserSessionId, $peerSessionId) {
                $q->where('sender_session_id', $currentUserSessionId)
                    ->where('receiver_session_id', $peerSessionId);
            })
                ->orWhere(function ($q) use ($peerSessionId, $currentUserSessionId) {
                    $q->where('sender_session_id', $peerSessionId)
                        ->where('receiver_session_id', $currentUserSessionId);
                });
        })
            ->where(function ($query) use ($currentUserSessionId) {
                // Filter out messages deleted by current user
                $query->where(function ($q) use ($currentUserSessionId) {
                    // If current user is sender, check deleted_for_sender
                    $q->where('sender_session_id', $currentUserSessionId)
                        ->where('deleted_for_sender', false);
                })
                    ->orWhere(function ($q) use ($currentUserSessionId) {
                        // If current user is receiver, check deleted_for_receiver
                        $q->where('receiver_session_id', $currentUserSessionId)
                            ->where('deleted_for_receiver', false);
                    });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'current_user_session_id' => $currentUserSessionId,
            'network_info' => [
                'protocol' => 'TCP',
                'method' => 'GET',
                'timestamp' => now()->toISOString()
            ]
        ]);
    }

    public function deleteForMe(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        $currentUserSessionId = (string) session('user_session_id');
        $messageSenderId = (string) $message->sender_session_id;
        $messageReceiverId = (string) $message->receiver_session_id;

        // Debug logging
        Log::info('Delete for me attempt:', [
            'message_id' => $messageId,
            'message_sender' => $messageSenderId,
            'message_receiver' => $messageReceiverId,
            'current_user' => $currentUserSessionId
        ]);

        // Check if user is sender or receiver
        if ($messageSenderId === $currentUserSessionId) {
            $message->deleted_for_sender = true;
        } elseif ($messageReceiverId === $currentUserSessionId) {
            $message->deleted_for_receiver = true;
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->save();

        return response()->json(['success' => true]);
    }

    public function deleteForEveryone(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        $currentUserSessionId = (string) session('user_session_id');
        $messageSenderId = (string) $message->sender_session_id;

        // Debug logging
        Log::info('Delete for everyone attempt:', [
            'message_id' => $messageId,
            'message_sender' => $messageSenderId,
            'current_user' => $currentUserSessionId,
            'match' => $messageSenderId === $currentUserSessionId
        ]);

        // Only sender can delete for everyone
        if ($messageSenderId !== $currentUserSessionId) {
            return response()->json([
                'error' => 'Only sender can delete for everyone',
                'debug' => [
                    'message_sender' => $messageSenderId,
                    'current_user' => $currentUserSessionId,
                    'match' => $messageSenderId === $currentUserSessionId
                ]
            ], 403);
        }

        // Physically delete the message from database (Option A)
        $message->delete();

        return response()->json(['success' => true, 'message_id' => $messageId]);
    }
}
