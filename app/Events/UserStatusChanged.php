<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

class UserStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        \Log::info('Evento UserStatusChanged emitido con datos:', [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'is_online' => $this->user->is_online,
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('user-status');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'is_online' => $this->user->is_online,
        ];
    }

    public function handle(Login $event)
    {
        $user = $event->user;
        $user->is_online = true;
        $user->save();

        broadcast(new UserStatusChanged($user));
        \Log::info('Evento UserStatusChanged emitido', ['user_id' => $user->id]); // Esto debería aparecer en storage/logs/laravel.log
    }

    public function broadcastAs()
    {
        return 'UserStatusChanged'; // Asegúrate de que el nombre coincida con lo que escuchas en el frontend
    }

}
