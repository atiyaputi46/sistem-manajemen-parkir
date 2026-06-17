<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    protected $fillable = [
        'slot_code',
        'vehicle_type',
        'floor',
        'zone',
        'status',
    ];
}
