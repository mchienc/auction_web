<?php

namespace App\Http\Controllers;

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
        // (Sẽ viết ở bước sau)
    }

    /**
     * Xử lý việc đặt giá.
     */
    public function placeBid(Request $request, Auction $auction)
    {
        // (Sẽ viết ở bước sau)
    }
}