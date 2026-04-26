<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('customer_id');
        $table->unsignedBigInteger('room_id');
        $table->date('checkin_date');
        $table->date('checkout_date');
        $table->decimal('total_price', 12, 2);
        $table->string('status')->default('confirmed');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
