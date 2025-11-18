<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\UserPin;

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

        $messages = Message::where(function ($query) use ($peerSessionId) {
            $query->where('sender_session_id', session('user_session_id'))
                ->where('receiver_session_id', $peerSessionId);
        })
            ->orWhere(function ($query) use ($peerSessionId) {
                $query->where('sender_session_id', $peerSessionId)
                    ->where('receiver_session_id', session('user_session_id'));
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'network_info' => [
                'protocol' => 'TCP',
                'method' => 'GET',
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
}
