<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\PrivateChat;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{privateChatId}', function ($user, $privateChatId) {
    $chat = PrivateChat::find($privateChatId);
    return $chat && ($chat->user_one_id === $user->id || $chat->user_two_id === $user->id);
});

