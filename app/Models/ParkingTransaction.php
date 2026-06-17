<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingTransaction extends Model
{
    protected $fillable = [
        'slot_id',
        'vehicle_plate',
        'vehicle_type',
        'owner_name',
        'entry_time',
        'exit_time',
        'snapshot_first_hour_rate',
        'snapshot_subsequent_hour_rate',
        'snapshot_daily_max_rate',
        'snapshot_fine_lost_ticket',
        'fee',
        'payment_method',
        'officer_name',
        'status',
    ];
}
