<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('auctions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->decimal('start_price', 15, 2);
        $table->decimal('current_price', 15, 2)->nullable();
        $table->timestamp('start_time')->nullable(); // <-- Thêm ->nullable()
        $table->timestamp('end_time')->nullable();   // <-- Thêm ->nullable()
        $table->foreignId('winner_id')->nullable()->constrained('users');
        $table->enum('status', ['pending', 'running', 'finished', 'cancelled'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auctions');
    }
};
