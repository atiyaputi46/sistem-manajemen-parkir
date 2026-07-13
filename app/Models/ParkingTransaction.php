<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Eloquent untuk data Transaksi Parkir (Parking Transaction).
 * Menyimpan data log masuk-keluar kendaraan, durasi parkir, snapshot tarif, nominal biaya, dan status parkir.
 */
class ParkingTransaction extends Model
{
    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
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

    /**
     * Relasi ke model ParkingSlot (Setiap transaksi terkait dengan satu slot parkir).
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(ParkingSlot::class);
    }
}
