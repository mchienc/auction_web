<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function bids() {
        return $this->hasMany(Bid::class)->orderBy('amount', 'desc');
    }

    public function winner() {
        return $this->belongsTo(User::class, 'winner_id');
    }
}