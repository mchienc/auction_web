<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\ProfileController; // Đảm bảo dòng này đúng

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuctionController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Routes cho Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes cho Product
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});

Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');
Route::post('/auctions/{auction}/bids', [AuctionController::class, 'placeBid'])->name('auctions.bids.store')->middleware('auth');
Route::get('/my-auctions', [AuctionController::class, 'myAuctions'])->name('auctions.mine');
Route::get('/won-auctions', [AuctionController::class, 'wonAuctions'])->name('auctions.won');
require __DIR__.'/auth.php';