<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;

    public function __construct(Bid $bid)
    {
        // Tải thông tin người dùng kèm theo lượt trả giá
        $this->bid = $bid->load('user');
    }

    public function broadcastOn()
    {
        // Phát sự kiện trên kênh của phiên đấu giá cụ thể
        return new Channel('auction.' . $this->bid->auction_id);
    }
}