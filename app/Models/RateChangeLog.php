<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $vehicle_type
 * @property int $changed_by
 * @property array<string, mixed> $old_rates
 * @property array<string, mixed> $new_rates
 * @property Carbon $created_at
 */
class RateChangeLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'vehicle_type',
        'changed_by',
        'old_rates',
        'new_rates',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_rates' => 'array',
            'new_rates' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
