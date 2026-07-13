<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Eloquent untuk data Tarif Parkir (Parking Rate).
 * Menyimpan pengaturan besaran tarif parkir jam pertama, jam berikutnya, maksimal harian, dan denda karcis hilang per jenis kendaraan.
 */
class ParkingRate extends Model
{
    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_type',
        'first_hour_rate',
        'subsequent_hour_rate',
        'daily_max_rate',
        'fine_lost_ticket',
    ];
}
