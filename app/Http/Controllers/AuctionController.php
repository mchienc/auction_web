<?php

namespace App\Http\Controllers;

use App\Events\NewBidPlaced;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Hiển thị danh sách các phiên đấu giá.
     */
    public function index()
    {
        // Lấy tất cả các phiên đấu giá đang chạy, sắp xếp theo thời gian tạo mới nhất
        $auctions = Auction::with('product') // Tải kèm thông tin sản phẩm để tránh N+1 query
            ->where('status', 'running')
            ->orderBy('created_at', 'desc')
            ->get();

        // Trả về view 'home' và truyền dữ liệu auctions vào
        return view('home', compact('auctions'));
    }

    /**
     * Hiển thị chi tiết một phiên đấu giá.
     */
    public function show(Auction $auction)
{
    // Tải các thông tin liên quan để tối ưu query và hiển thị
    $auction->load('product.user', 'bids.user');

    return view('auctions.show', compact('auction'));
}

    /**
     * Xử lý việc đặt giá.
     */
    public function placeBid(Request $request, Auction $auction)
{
    // 1. Validate dữ liệu
    $request->validate([
        'amount' => 'required|numeric|min:' . ($auction->current_price + 1),
    ]);

    // 2. Kiểm tra xem phiên đấu giá còn hoạt động không
    if ($auction->end_time < now() || $auction->status !== 'running') {
        return back()->with('error', 'Phiên đấu giá đã kết thúc.');
    }

    // 3. Không cho phép người đăng sản phẩm tự trả giá
    if ($auction->product->user_id == auth()->id()) {
        return back()->with('error', 'Bạn không thể tự trả giá cho sản phẩm của mình.');
    }

    // 4. Tạo một lượt đặt giá mới
    $bid = new \App\Models\Bid();
    $bid->user_id = auth()->id();
    $bid->auction_id = $auction->id;
    $bid->amount = $request->amount;
    $bid->save();

    // 5. Cập nhật lại giá hiện tại của phiên đấu giá
    $auction->current_price = $request->amount;
    $auction->save();

    return back()->with('success', 'Đặt giá thành công!');
}
}