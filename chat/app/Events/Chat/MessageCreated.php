<?php

namespace App\Events\Chat;

use App\Models\Chat\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, $conversation_id)
    {
        $this->message = $message;
        $this->conversation_id = $conversation_id;
    }

    public function broadcastWith()
    {
        $this->message->load(['user']);
        $this->message->conversation_id = $this->conversation_id;
        return [
            'message' => array_merge($this->message->toArray(), ['selfOwned' => false])
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('chat');
    }
}
