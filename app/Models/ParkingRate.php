<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingRate extends Model
{
    protected $fillable = [
        'vehicle_type',
        'first_hour_rate',
        'subsequent_hour_rate',
        'daily_max_rate',
        'fine_lost_ticket',
    ];
}
