<?php

namespace App\Events;

use App\Models\Poll;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pollHtml;
    public $status;

    public function __construct($pollHtml = null, $status = null)
    {
        $this->pollHtml = $pollHtml;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel('poll-channel');
    }

    public function broadcastAs()
    {
        return 'poll-status-changed';
    }
}
