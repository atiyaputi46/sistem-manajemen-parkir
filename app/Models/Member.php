<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Eloquent untuk data Member langganan parkir.
 * Menyimpan informasi profil member, masa aktif langganan, dan status keanggotaan.
 */
class Member extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
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
