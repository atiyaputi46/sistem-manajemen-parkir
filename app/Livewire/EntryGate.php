<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Entry Gate')]
class EntryGate extends Component
{
    // Step 1: Plat Nomor
    public string $vehiclePlate = '';

    /** @var array<string, string>|null */
    public ?array $activeMember = null;

    public bool $isDuplicate = false;

    // Step 2: Jenis & Slot
    public string $vehicleType = 'motor';

    public ?int $selectedSlotId = null;

    // Step 3: Hasil transaksi
    public bool $showTicket = false;

    /** @var array<string, mixed>|null */
    public ?array $lastTransaction = null;

    /**
     * Slot tersedia — dihitung ulang tiap render, tidak disimpan sebagai property.
     *
     * @return Collection<int, ParkingSlot>
     */
    #[Computed]
    public function availableSlots(): Collection
    {
        return ParkingSlot::where('status', 'available')
            ->where('vehicle_type', $this->vehicleType)
            ->orderBy('slot_code')
            ->limit(5)
            ->get();
    }

    /**
     * Dipanggil setiap kali $vehiclePlate berubah (via wire:model.live.debounce.400ms).
     */
    public function updatedVehiclePlate(string $value): void
    {
        $plate = strtoupper(trim($value));
        $this->isDuplicate = false;
        $this->activeMember = null;

        if (strlen($plate) < 4) {
            return;
        }

        $member = Member::where('vehicle_plate', $plate)
            ->where('status', 'active')
            ->where('subscription_end', '>=', Carbon::today())
            ->first();

        $this->activeMember = $member ? $member->only(['full_name', 'vehicle_plate']) : null;

        $this->isDuplicate = ParkingTransaction::where('vehicle_plate', $plate)
            ->where('status', 'parked')
            ->exists();
    }

    /**
     * Reset pilihan slot saat jenis kendaraan berubah.
     */
    public function updatedVehicleType(): void
    {
        $this->selectedSlotId = null;
        unset($this->availableSlots);
    }

    public function selectSlot(int $slotId): void
    {
        $this->selectedSlotId = $slotId;
    }

    /**
     * Proses entry kendaraan: validasi, ambil tarif, simpan transaksi, update slot.
     */
    public function confirmEntry(): void
    {
        $plate = strtoupper(trim($this->vehiclePlate));

        $this->validate([
            'vehiclePlate' => ['required', 'min:4'],
            'vehicleType' => ['required', 'in:motor,mobil,truk'],
            'selectedSlotId' => ['required', 'integer'],
        ]);

        $duplicate = ParkingTransaction::where('vehicle_plate', $plate)
            ->where('status', 'parked')
            ->exists();

        if ($duplicate) {
            $this->addError('vehiclePlate', 'Kendaraan ini sudah tercatat masuk.');

            return;
        }

        $slot = ParkingSlot::where('id', $this->selectedSlotId)
            ->where('status', 'available')
            ->first();

        if (! $slot) {
            $this->addError('selectedSlotId', 'Slot tidak tersedia lagi. Silakan pilih slot lain.');
            unset($this->availableSlots);

            return;
        }

        $rate = ParkingRate::where('vehicle_type', $this->vehicleType)->first();

        if (! $rate) {
            $this->addError('vehicleType', 'Tarif untuk jenis kendaraan ini belum dikonfigurasi.');

            return;
        }

        $transaction = DB::transaction(function () use ($plate, $slot, $rate): ParkingTransaction {
            $tx = ParkingTransaction::create([
                'slot_id' => $this->selectedSlotId,
                'vehicle_plate' => $plate,
                'vehicle_type' => $this->vehicleType,
                'entry_time' => now(),
                'status' => 'parked',
                'snapshot_first_hour_rate' => $rate->first_hour_rate,
                'snapshot_subsequent_hour_rate' => $rate->subsequent_hour_rate,
                'snapshot_daily_max_rate' => $rate->daily_max_rate,
                'snapshot_fine_lost_ticket' => $rate->fine_lost_ticket,
                'officer_name' => Auth::user()->name,
            ]);

            $slot->update(['status' => 'occupied']);

            return $tx;
        });

        $transaction->load('slot');

        // Simpan sebagai array plain agar bisa di-serialize Livewire
        $this->lastTransaction = [
            'id' => $transaction->id,
            'vehicle_plate' => $transaction->vehicle_plate,
            'vehicle_type' => $transaction->vehicle_type,
            'entry_time' => $transaction->entry_time,
            'officer_name' => $transaction->officer_name,
            'slot_code' => $transaction->slot?->slot_code,
        ];

        $this->showTicket = true;
        unset($this->availableSlots);
    }

    /**
     * Reset semua state ke kondisi awal.
     */
    public function resetForm(): void
    {
        $this->vehiclePlate = '';
        $this->activeMember = null;
        $this->isDuplicate = false;
        $this->vehicleType = 'motor';
        $this->selectedSlotId = null;
        $this->lastTransaction = null;
        $this->showTicket = false;
        unset($this->availableSlots);
    }

    public function render(): View
    {
        return view('livewire.entry-gate');
    }
}
