<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileTransfer;
use App\Models\UserPin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileTransferController extends Controller
{
    public function index()
    {
        $files = FileTransfer::where('sender_session_id', session('user_session_id'))
            ->orWhere('receiver_session_id', session('user_session_id'))
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

        // Check if user is sender or receiver
        if (
            $fileTransfer->sender_session_id !== session('user_session_id') &&
            $fileTransfer->receiver_session_id !== session('user_session_id')
        ) {
            abort(403, 'Unauthorized');
        }

        $filePath = storage_path('app/public/' . $fileTransfer->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $fileTransfer->original_name);
    }
}
