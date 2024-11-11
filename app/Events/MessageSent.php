<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\PrivateMessage;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PrivateMessage $message;

    public function __construct(PrivateMessage $message)
    {
        $this->message = $message;

        \Log::info('Evento MessageSent emitido', ['message' => $message]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->private_chat_id);
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->message->user_id,
            'content' => $this->message->content,
            'user_name' => $this->message->user->name,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

}
