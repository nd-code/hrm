<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Public channel for ALL employees
        return new Channel('notifications');
    }

    /**
     * The event name that will be received on frontend.
     */
    public function broadcastAs()
    {
        return 'new-notification';
    }
}
