<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_session_id',
        'receiver_session_id',
        'message',
        'message_type',
        'sender_ip',
        'receiver_ip',
        'is_delivered',
        'deleted_for_sender',
        'deleted_for_receiver',
        'deleted_for_everyone',
        'file_download_url'
    ];

    protected $casts = [
        'is_delivered' => 'boolean',
        'deleted_for_sender' => 'boolean',
        'deleted_for_receiver' => 'boolean',
        'deleted_for_everyone' => 'boolean'
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
