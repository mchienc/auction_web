<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuctionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route cho trang chủ, sẽ hiển thị danh sách các phiên đấu giá
Route::get('/', [AuctionController::class, 'index'])->name('home');

// Route để hiển thị trang dashboard sau khi đăng nhập
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Nhóm các route yêu cầu người dùng phải đăng nhập
Route::middleware('auth')->group(function () {
    // Route để hiển thị form tạo sản phẩm và phiên đấu giá
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    // Route để xử lý việc lưu sản phẩm và tạo phiên đấu giá
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});

// Route để xem chi tiết một phiên đấu giá
Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');

// Route để người dùng đặt giá (cũng yêu cầu đăng nhập)
Route::post('/auctions/{auction}/bids', [AuctionController::class, 'placeBid'])->name('auctions.bids.store')->middleware('auth');


// Dòng này sẽ tự động thêm các route cần thiết cho việc đăng nhập, đăng ký... của Laravel Breeze
require __DIR__.'/auth.php';