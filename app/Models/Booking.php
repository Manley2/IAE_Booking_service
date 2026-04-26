<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'room_id',
        'checkin_date',
        'checkout_date',
        'total_price',
        'status',
            ];
}
