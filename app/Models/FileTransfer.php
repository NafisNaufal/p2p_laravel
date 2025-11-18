<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileTransfer extends Model
{
    protected $fillable = [
        'sender_session_id',
        'receiver_session_id',
        'filename',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'file_hash',
        'sender_ip',
        'receiver_ip',
        'transfer_status'
    ];

    public function sender()
    {
        return $this->belongsTo(UserPin::class, 'sender_session_id', 'session_id');
    }

    public function receiver()
    {
        return $this->belongsTo(UserPin::class, 'receiver_session_id', 'session_id');
    }
}
