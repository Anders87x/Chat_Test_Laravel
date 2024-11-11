<?php

namespace App\Listeners;

use IlluminateAuthEventsLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Login;
use App\Events\UserStatusChanged;

class UpdateUserStatusOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(Login $event): void
    {
        \Log::info('Listener de inicio de sesión activado'); 
        $user = $event->user;
        $user->is_online = true;
        $user->save();

        broadcast(new UserStatusChanged($user));
    }
}
