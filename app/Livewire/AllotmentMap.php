<?php

namespace App\Livewire;

use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Allotment Map')]
class AllotmentMap extends Component
{
    /** Filter jenis kendaraan: 'all' | 'motor' | 'mobil' | 'truk' */
    public string $filter = 'all';

    /**
     * Data slot yang sedang dipilih (status=occupied) untuk ditampilkan di modal.
     *
     * @var array<string, mixed>|null
     */
    public ?array $selectedSlot = null;

    /**
     * Data transaksi aktif untuk slot yang dipilih.
     *
     * @var array<string, mixed>|null
     */
    public ?array $activeTransaction = null;

    /**
     * Slot yang ditampilkan berdasarkan filter aktif.
     *
     * @return Collection<int, ParkingSlot>
     */
    public function parkingSlots(): Collection
    {
        return $this->filter === 'all'
            ? ParkingSlot::orderBy('slot_code')->get()
            : ParkingSlot::where('vehicle_type', $this->filter)->orderBy('slot_code')->get();
    }

    /** Set filter dan tutup modal yang mungkin terbuka. */
    public function setFilter(string $value): void
    {
        $this->filter = $value;
        $this->selectedSlot = null;
        $this->activeTransaction = null;
    }

    /**
     * Klik slot occupied → tampilkan detail transaksi di modal.
     */
    public function selectSlot(int $slotId): void
    {
        $slot = ParkingSlot::find($slotId);

        if (! $slot || $slot->status !== 'occupied') {
            return;
        }

        $this->selectedSlot = $slot->toArray();

        $tx = ParkingTransaction::where('slot_id', $slotId)
            ->where('status', 'parked')
            ->latest('entry_time')
            ->first();

        if ($tx) {
            $entryTime = Carbon::parse($tx->entry_time);
            $duration = $entryTime->diff(now());
            $durationText = '';

            if ($duration->h > 0 || $duration->days > 0) {
                $totalHours = ($duration->days * 24) + $duration->h;
                $durationText = $totalHours.' jam '.$duration->i.' menit';
            } else {
                $durationText = $duration->i.' menit';
            }

            $this->activeTransaction = [
                'vehicle_plate' => $tx->vehicle_plate,
                'entry_time' => $entryTime->format('d M Y H:i'),
                'duration' => $durationText,
            ];
        } else {
            $this->activeTransaction = null;
        }
    }

    /** Tutup modal detail slot. */
    public function closeModal(): void
    {
        $this->selectedSlot = null;
        $this->activeTransaction = null;
    }

    /**
     * Manual override status slot oleh admin.
     * Hanya izinkan target status: 'available', 'reserved', 'disabled'.
     */
    public function overrideSlotStatus(int $slotId, string $newStatus): void
    {
        $allowed = ['available', 'reserved', 'disabled'];

        if (! in_array($newStatus, $allowed, true)) {
            return;
        }

        $slot = ParkingSlot::find($slotId);

        if (! $slot) {
            return;
        }

        $slot->update(['status' => $newStatus]);

        Log::info("Admin override slot {$slot->slot_code} to {$newStatus} by ".auth()->user()->name);

        // Tutup modal jika slot yang di-override sedang terbuka
        if ($this->selectedSlot && (int) $this->selectedSlot['id'] === $slotId) {
            $this->selectedSlot = null;
            $this->activeTransaction = null;
        }

        // Dispatch event agar grid me-refresh dirinya sendiri
        $this->dispatch('slot-updated');
    }

    public function render(): View
    {
        return view('livewire.allotment-map', [
            'parkingSlots' => $this->parkingSlots(),
        ]);
    }
}
