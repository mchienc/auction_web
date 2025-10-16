<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// THÊM ĐOẠN NÀY VÀO
// Cho phép tất cả người dùng (kể cả khách) lắng nghe kênh đấu giá công khai
Broadcast::channel('auction.{auctionId}', function ($user, $auctionId) {
    return true;
});