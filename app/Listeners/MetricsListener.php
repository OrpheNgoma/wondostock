<?php

namespace App\Listeners;

use App\Services\MetricsService;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;

class MetricsListener implements ShouldQueue
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        
        if ($user->company_id) {
            MetricsService::recordLogin($user->id, $user->company_id);
        }
    }

    /**
     * Register event listeners.
     */
    public function subscribe($events): void
    {
        $events->listen(Login::class, [MetricsListener::class, 'handleLogin']);
    }
}
