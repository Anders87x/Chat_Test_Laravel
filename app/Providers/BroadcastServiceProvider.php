<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        // Registra las rutas de Broadcast con el middleware `auth`
        Broadcast::routes(['middleware' => ['auth']]);

        require base_path('routes/channels.php');

    }
}
