<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewBidPlaced;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Lấy danh sách các phiên đấu giá, cùng với thông tin sản phẩm
        // và sắp xếp theo ngày tạo mới nhất, sau đó phân trang
        $auctions = Auction::with('product')->latest()->paginate(9);
        return view('home', compact('auctions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Auction  $auction
     * @return \Illuminate\Http\Response
     */
    public function show(Auction $auction)
    {
        // Tải thông tin liên quan: sản phẩm (cùng với người bán), 
        // và các lượt trả giá (cùng với người trả giá)
        $auction->load('product.user', 'bids.user');
        return view('auctions.show', compact('auction'));
    }

    /**
     * Store a newly created bid in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auction  $auction
     * @return \Illuminate\Http\Response
     */
    public function placeBid(Request $request, Auction $auction)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để trả giá.');
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'amount' => 'required|numeric|min:' . ($auction->current_price + 1),
        ]);

        // Kiểm tra xem phiên đấu giá còn hoạt động không
        if (now()->gt($auction->end_time)) {
            return redirect()->back()->with('error', 'Phiên đấu giá đã kết thúc.');
        }
        
        // Kiểm tra xem người bán có tự trả giá sản phẩm của mình không
        if ($auction->product->user_id == Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không thể trả giá cho sản phẩm của chính mình.');
        }

        // Tạo một lượt trả giá mới
        $bid = new Bid();
        $bid->user_id = Auth::id();
        $bid->auction_id = $auction->id;
        $bid->amount = $request->amount;
        $bid->save();

        // Cập nhật giá hiện tại của phiên đấu giá
        $auction->current_price = $request->amount;
        $auction->save();
        
        // (Tùy chọn) Bạn có thể thêm sự kiện (event) ở đây để thông báo real-time
        event(new NewBidPlaced($bid));

        return redirect()->back()->with('success', 'Bạn đã trả giá thành công.');
    }

    /**
     * Display the auctions created by the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myAuctions()
    {
        // Lấy danh sách các phiên đấu giá mà người dùng hiện tại đã tạo
        $auctions = Auction::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('product')->latest()->paginate(9);

        return view('auctions.my_auctions', compact('auctions'));
    }

    /**
     * Display the auctions won by the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function wonAuctions()
    {
        // Lấy danh sách các phiên đấu giá mà người dùng hiện tại đã thắng
         $auctions = Auction::where('status', 'finished')
            ->whereHas('bids', function ($query) {
                $query->where('user_id', Auth::id())
                      ->whereRaw('amount = (select max(amount) from bids where auction_id = auctions.id)');
            })
            ->with('product')
            ->latest()
            ->paginate(9);

        return view('auctions.won_auctions', compact('auctions'));
    }
}