<?php

namespace App\Events;

use App\Eloquent\Customer;
use App\Eloquent\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class FreshItemsFound
 * @package App\Events
 * @property array $links
 * @property Question $filter
 */
class FreshItemsFound
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $links;
    public $filter;

    public function __construct(array $links, Question $filter)
    {
        $this->links = $links;
        $this->filter = $filter;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
