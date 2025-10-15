<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionWonNotification extends Notification
{
    use Queueable;

    protected $auction;

    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Chúc mừng! Bạn đã thắng phiên đấu giá')
                    ->greeting('Xin chào ' . $notifiable->name . ',')
                    ->line('Chúc mừng bạn đã là người chiến thắng trong phiên đấu giá cho sản phẩm: ' . $this->auction->product->name)
                    ->line('Giá cuối cùng của bạn là: ' . number_format($this->auction->current_price) . ' VND.')
                    ->action('Xem lại sản phẩm', route('auctions.show', $this->auction))
                    ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }
}