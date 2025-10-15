<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;
}
public function product() {
    return $this->belongsTo(Product::class);
}

public function bids() {
    return $this->hasMany(Bid::class)->orderBy('amount', 'desc'); // Sắp xếp các lượt bid
}

public function winner() {
    return $this->belongsTo(User::class, 'winner_id');
}