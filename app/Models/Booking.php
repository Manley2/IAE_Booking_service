<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'room_id',
        'check_in',
        'check_out',
        'status',
    ];
}
