<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model Eloquent untuk data Log Perubahan Tarif (Rate Change Log).
 * Digunakan untuk merekam riwayat tarif lama dan tarif baru saat Admin mengubah konfigurasi tarif parkir.
 *
 * @property int $id
 * @property string $vehicle_type
 * @property int $changed_by
 * @property array<string, mixed> $old_rates
 * @property array<string, mixed> $new_rates
 * @property Carbon $created_at
 */
class RateChangeLog extends Model
{
    /**
     * Menonaktifkan pengelolaan kolom otomatis created_at & updated_at bawaan Eloquent.
     * Kita hanya menggunakan custom kolom 'created_at'.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_type',
        'changed_by',
        'old_rates',
        'new_rates',
        'created_at',
    ];

    /**
     * Mengatur casting tipe data database ke tipe PHP spesifik saat diakses.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_rates' => 'array',
            'new_rates' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke model User (Setiap perubahan tarif dicatat berdasarkan User admin yang mengubahnya).
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
