<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $fillable = ['private_chat_id', 'user_id', 'content'];

    // Indica a Laravel que no maneje `updated_at`
    const UPDATED_AT = null;

    // Relación con la tabla `private_chats`
    public function chat()
    {
        return $this->belongsTo(PrivateChat::class, 'private_chat_id');
    }

    // Relación con la tabla `users`
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
