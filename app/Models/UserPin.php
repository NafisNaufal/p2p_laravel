<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserPin extends Model
{
    protected $fillable = [
        'session_id',
        'pin_hash',
        'username',
        'ip_address',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime'
    ];

    public function setPin(string $pin): void
    {
        $this->pin_hash = Hash::make($pin);
    }

    public function verifyPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin_hash);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_session_id', 'session_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_session_id', 'session_id');
    }
}
