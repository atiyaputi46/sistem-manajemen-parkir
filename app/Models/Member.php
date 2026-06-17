<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'full_name',
        'vehicle_plate',
        'vehicle_type',
        'phone',
        'subscription_start',
        'subscription_end',
        'status',
    ];
}
