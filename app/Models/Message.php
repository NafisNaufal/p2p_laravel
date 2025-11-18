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
        'is_delivered'
    ];

    protected $casts = [
        'is_delivered' => 'boolean'
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
