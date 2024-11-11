<?php

namespace App\Listeners;

use IlluminateAuthEventsLogout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Logout;
use App\Events\UserStatusChanged;

class UpdateUserStatusOnLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        \Log::info('Listener de Cierre de sesión activado');
        $user = $event->user;
        if ($user) {
            $user->is_online = false;
            $user->save();

            broadcast(new UserStatusChanged($user));
        }
    }
}
