<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Eloquent untuk data Slot Parkir (Parking Slot).
 * Menyimpan informasi kode slot, tipe kendaraan yang diizinkan, lantai, zona, dan status ketersediaan slot parkir fisik.
 */
class ParkingSlot extends Model
{
    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slot_code',
        'vehicle_type',
        'floor',
        'zone',
        'status',
    ];
}
