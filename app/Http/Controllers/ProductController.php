<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị form để tạo sản phẩm mới.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Lưu sản phẩm mới và tạo phiên đấu giá tương ứng.
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu người dùng nhập vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_price' => 'required|numeric|min:0',
            'end_time' => 'required|date|after:now',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Cho phép upload ảnh
        ]);

        // 2. Xử lý upload file ảnh (nếu có)
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Lưu ảnh vào thư mục 'public/products'
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 3. Tạo sản phẩm mới
        $product = new Product();
        $product->user_id = auth()->id(); // Lấy id của user đang đăng nhập
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'];
        $product->image = $imagePath;
        $product->save();

        // 4. Tạo phiên đấu giá gắn với sản phẩm vừa tạo
        $auction = new Auction();
        $auction->product_id = $product->id;
        $auction->start_price = $validatedData['start_price'];
        $auction->current_price = $validatedData['start_price']; // Giá hiện tại ban đầu bằng giá khởi điểm
        $auction->start_time = now(); // Bắt đầu ngay lập tức
        $auction->end_time = $validatedData['end_time'];
        $auction->status = 'running';
        $auction->save();

        // 5. Chuyển hướng về trang chủ với thông báo thành công
        return redirect()->route('home')->with('success', 'Tạo phiên đấu giá thành công!');
    }
}