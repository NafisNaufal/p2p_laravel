<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileTransfer;
use App\Models\UserPin;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileTransferController extends Controller
{
    public function index(Request $request)
    {
        $userSessionId = session('user_session_id');
        $peerSessionId = $request->query('peer_session_id');

        if (!$userSessionId) {
            return response()->json(['files' => []]);
        }

        // Only show files sent BY the current user TO the active peer
        $query = FileTransfer::where('sender_session_id', $userSessionId);

        // Filter by receiver (active peer) if peer_session_id is provided
        if ($peerSessionId) {
            $query->where('receiver_session_id', $peerSessionId);
        }

        $files = $query->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['files' => $files]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|max:10240', // 10MB max
            'receiver_session_id' => 'required|string'
        ]);

        // Verify receiver exists
        $receiver = UserPin::where('session_id', $request->receiver_session_id)
            ->where('is_active', true)
            ->first();

        if (!$receiver) {
            return response()->json(['error' => 'Peer not found or inactive'], 404);
        }

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        $fileTransfer = FileTransfer::create([
            'sender_session_id' => session('user_session_id'),
            'receiver_session_id' => $request->receiver_session_id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_hash' => hash_file('sha256', $file->getRealPath()),
            'sender_ip' => $request->ip(),
            'receiver_ip' => $receiver->ip_address,
            'transfer_status' => 'completed'
        ]);

        // Send file notification message to receiver only
        $downloadUrl = "/files/{$fileTransfer->id}/download";
        $sender = UserPin::where('session_id', session('user_session_id'))->first();

        // Create message notification for receiver
        // Mark as deleted_for_sender = true so it ONLY appears in receiver's chat
        Message::create([
            'sender_session_id' => session('user_session_id'), // Real sender
            'receiver_session_id' => $request->receiver_session_id, // Real receiver
            'message' => '📁 File received - ' . $file->getClientOriginalName(),
            'message_type' => 'file',
            'sender_ip' => $request->ip(),
            'receiver_ip' => $receiver->ip_address,
            'file_download_url' => $downloadUrl,
            'deleted_for_sender' => true // Hide from sender's chat
        ]);

        return response()->json([
            'success' => true,
            'file' => $fileTransfer,
            'network_info' => [
                'protocol' => 'TCP',
                'method' => 'POST',
                'content_type' => 'multipart/form-data',
                'file_size' => $file->getSize(),
                'transfer_time' => now()->toISOString()
            ]
        ]);
    }

    public function download($id)
    {
        $fileTransfer = FileTransfer::findOrFail($id);

        // Allow download without session authentication for cross-device access
        // File ID serves as the security token

        $filePath = storage_path('app/public/' . $fileTransfer->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $fileTransfer->original_name);
    }
}
