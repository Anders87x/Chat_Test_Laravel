<?php

namespace App\Listeners;

use App\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastMessage
{

    public function __construct()
    {

    }

    public function handle(MessageSent $event): void
    {
        broadcast($event)->toOthers();
        \Log::info('Evento MessageSent emitido desde el listener', ['message_id' => $event->message->id]);
    }
}
