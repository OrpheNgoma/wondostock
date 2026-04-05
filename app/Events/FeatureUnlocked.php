<?php

namespace App\Events;

use App\Models\FeatureLock;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeatureUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public FeatureLock $featureLock,
        public ?User $admin = null
    ) {
        //
    }
}
