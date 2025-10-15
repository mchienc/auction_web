<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\User;
use App\Notifications\AuctionWonNotification;
use Illuminate\Console\Command;

class FinishAuctions extends Command
{
    protected $signature = 'auctions:finish';
    protected $description = 'Finish auctions that have ended and notify winners';

    public function handle()
    {
        $this->info('Checking for auctions to finish...');

        // Tìm tất cả các phiên đấu giá đang chạy và đã hết thời gian
        $endedAuctions = Auction::where('status', 'running')
                                ->where('end_time', '<=', now())
                                ->get();

        if ($endedAuctions->isEmpty()) {
            $this->info('No auctions to finish.');
            return 0;
        }

        foreach ($endedAuctions as $auction) {
            // Tìm người trả giá cao nhất
            $winnerBid = $auction->bids()->orderBy('amount', 'desc')->first();

            $auction->status = 'finished';
            if ($winnerBid) {
                $auction->winner_id = $winnerBid->user_id;
            }
            $auction->save();

            $this->info("Processed Auction #{$auction->id} for product '{$auction->product->name}'.");

            // Gửi thông báo cho người thắng cuộc
            if ($auction->winner_id) {
                $winner = User::find($auction->winner_id);
                if ($winner) {
                    $winner->notify(new AuctionWonNotification($auction));
                    $this->info("Notification sent to winner: {$winner->name}");
                }
            }
        }

        $this->info('All ended auctions have been processed.');
        return 0;
    }
}