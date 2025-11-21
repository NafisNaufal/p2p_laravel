<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\P2PController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FileTransferController;

// P2P Routes
Route::get('/', [P2PController::class, 'showPinSetup'])->name('pin.setup');
Route::post('/setup-pin', [P2PController::class, 'setupPin'])->name('pin.store');
Route::get('/login', [P2PController::class, 'showPinLogin'])->name('pin.login');
Route::post('/login', [P2PController::class, 'loginPin'])->name('pin.authenticate');

// Public File Download Route (for cross-device access)
Route::get('/files/{id}/download', [FileTransferController::class, 'download'])->name('files.download');

// Protected P2P Routes
Route::middleware('p2p.auth')->group(function () {
    Route::get('/dashboard', [P2PController::class, 'index'])->name('p2p.dashboard');
    Route::post('/logout', [P2PController::class, 'logout'])->name('p2p.logout');

    // Messaging Routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/fetch', [MessageController::class, 'fetch'])->name('messages.fetch');
    Route::delete('/messages/{messageId}/delete-for-me', [MessageController::class, 'deleteForMe'])->name('messages.deleteForMe');
    Route::delete('/messages/{messageId}/delete-for-everyone', [MessageController::class, 'deleteForEveryone'])->name('messages.deleteForEveryone');

    // File Transfer Routes
    Route::post('/files/upload', [FileTransferController::class, 'upload'])->name('files.upload');
    Route::get('/files', [FileTransferController::class, 'index'])->name('files.index');
});
