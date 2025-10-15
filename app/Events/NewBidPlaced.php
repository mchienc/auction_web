<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewBidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;

    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

    public function broadcastOn()
    {
        return new Channel('auction.' . $this->bid->auction_id);
    }

    public function broadcastWith()
    {
        // Tải thông tin user để gửi đi
        $this->bid->load('user');

        return [
            'bid' => [
                'amount' => $this->bid->amount,
                'user_name' => $this->bid->user->name,
                'created_at' => $this->bid->created_at->format('H:i:s'),
            ],
        ];
    }
}