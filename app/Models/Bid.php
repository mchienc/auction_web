<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{ // <-- Thêm dấu {
    use HasFactory; // <-- Thêm dòng này

    public function auction() {
        return $this->belongsTo(Auction::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}