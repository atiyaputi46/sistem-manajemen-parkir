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
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Entry Gate')]
class EntryGate extends Component
{
    // Step 1: Plat Nomor
    public string $vehiclePlate = '';

    /** @var object|null */
    public $activeMember = null;

    public bool $isDuplicate = false;

    // Step 2: Jenis & Slot
    public string $vehicleType = 'motor';

    /** @var Collection<int, ParkingSlot> */
    public $availableSlots;

    public ?int $selectedSlotId = null;

    // Step 3: Hasil transaksi
    /** @var ParkingTransaction|null */
    public $lastTransaction = null;

    public bool $showTicket = false;

    public function mount(): void
    {
        $this->availableSlots = collect();
        $this->loadAvailableSlots();
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

        // Cek member aktif
        $member = Member::where('vehicle_plate', $plate)
            ->where('status', 'active')
            ->where('subscription_end', '>=', Carbon::today())
            ->first();

        $this->activeMember = $member ? $member->only(['full_name', 'vehicle_plate']) : null;

        // Cek duplikat transaksi aktif
        $this->isDuplicate = ParkingTransaction::where('vehicle_plate', $plate)
            ->where('status', 'parked')
            ->exists();
    }

    /**
     * Dipanggil setiap kali $vehicleType berubah (via wire:model.live).
     */
    public function updatedVehicleType(): void
    {
        $this->selectedSlotId = null;
        $this->loadAvailableSlots();
    }

    public function loadAvailableSlots(): void
    {
        $this->availableSlots = ParkingSlot::where('status', 'available')
            ->where('vehicle_type', $this->vehicleType)
            ->orderBy('slot_code')
            ->limit(5)
            ->get();
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

        // Validasi ulang di server
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
            $this->loadAvailableSlots();

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

        // Load relasi slot untuk karcis
        $transaction->load('slot');
        $this->lastTransaction = $transaction;
        $this->showTicket = true;
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
        $this->availableSlots = collect();
        $this->selectedSlotId = null;
        $this->lastTransaction = null;
        $this->showTicket = false;
    }

    public function render(): View
    {
        return view('livewire.entry-gate');
    }
}
