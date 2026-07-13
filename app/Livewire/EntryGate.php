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

/**
 * Komponen Livewire untuk Gerbang Masuk (Entry Gate POS).
 * Menangani input plat nomor kendaraan, deteksi member aktif, verifikasi parkir ganda,
 * rekomendasi slot parkir kosong terdekat, dan proses cetak tiket masuk.
 */
#[Title('Entry Gate')]
class EntryGate extends Component
{
    /**
     * Plat nomor kendaraan yang diinput oleh petugas.
     */
    public string $vehiclePlate = '';

    /**
     * Menyimpan informasi nama dan plat member jika terdeteksi aktif.
     *
     * @var array<string, string>|null
     */
    public ?array $activeMember = null;

    /**
     * Flag penanda apakah plat nomor yang dimasukkan sudah tercatat sedang terparkir.
     */
    public bool $isDuplicate = false;

    /**
     * Jenis kendaraan yang masuk: 'motor', 'mobil', atau 'truk'.
     */
    public string $vehicleType = 'motor';

    /**
     * ID slot parkir yang dipilih oleh petugas.
     */
    public ?int $selectedSlotId = null;

    /**
     * Status penampilan modal/pop-up cetak karcis setelah transaksi masuk berhasil.
     */
    public bool $showTicket = false;

    /**
     * Menyimpan rincian data transaksi masuk yang baru saja berhasil diproses.
     *
     * @var array<string, mixed>|null
     */
    public ?array $lastTransaction = null;

    /**
     * Mengambil daftar slot parkir kosong (available) terdekat (limit 5).
     * Dihitung ulang secara dinamis dan reaktif setiap kali view dirender.
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
     * Dipanggil otomatis oleh Livewire setiap kali properti $vehiclePlate berubah.
     * Digunakan untuk memvalidasi duplikasi plat parkir dan mendeteksi keanggotaan member.
     *
     * @param  string  $value  Nilai plat nomor kendaraan yang baru
     */
    public function updatedVehiclePlate(string $value): void
    {
        $plate = strtoupper(trim($value));
        $this->isDuplicate = false;
        $this->activeMember = null;

        // Validasi minimal panjang plat nomor
        if (strlen($plate) < 4) {
            return;
        }

        // Cek apakah plat nomor terdaftar sebagai member aktif dan masa berlakunya belum habis
        $member = Member::where('vehicle_plate', $plate)
            ->where('status', 'active')
            ->where('subscription_end', '>=', Carbon::today())
            ->first();

        $this->activeMember = $member ? $member->only(['full_name', 'vehicle_plate']) : null;

        // Cek apakah kendaraan ini sudah tercatat masuk dan belum keluar (parked)
        $this->isDuplicate = ParkingTransaction::where('vehicle_plate', $plate)
            ->where('status', 'parked')
            ->exists();
    }

    /**
     * Mengatur ulang pilihan slot parkir apabila jenis kendaraan diubah oleh petugas.
     */
    public function updatedVehicleType(): void
    {
        $this->selectedSlotId = null;
        unset($this->availableSlots);
    }

    /**
     * Memilih slot parkir tertentu untuk kendaraan yang akan masuk.
     *
     * @param  int  $slotId  ID slot parkir yang dipilih
     */
    public function selectSlot(int $slotId): void
    {
        $this->selectedSlotId = $slotId;
    }

    /**
     * Memproses masuknya kendaraan: validasi form, pengecekan slot,
     * pengambilan tarif aktif, pembuatan transaksi parkir, dan mengubah status slot menjadi terisi.
     */
    public function confirmEntry(): void
    {
        $plate = strtoupper(trim($this->vehiclePlate));

        // Melakukan validasi input petugas
        $this->validate([
            'vehiclePlate' => ['required', 'min:4'],
            'vehicleType' => ['required', 'in:motor,mobil,truk'],
            'selectedSlotId' => ['required', 'integer'],
        ]);

        // Proteksi ganda untuk mencegah masuknya plat nomor yang sama sekaligus
        $duplicate = ParkingTransaction::where('vehicle_plate', $plate)
            ->where('status', 'parked')
            ->exists();

        if ($duplicate) {
            $this->addError('vehiclePlate', 'Kendaraan ini sudah tercatat masuk.');

            return;
        }

        // Pastikan slot yang dipilih memang masih berstatus kosong (available)
        $slot = ParkingSlot::where('id', $this->selectedSlotId)
            ->where('status', 'available')
            ->first();

        if (! $slot) {
            $this->addError('selectedSlotId', 'Slot tidak tersedia lagi. Silakan pilih slot lain.');
            unset($this->availableSlots);

            return;
        }

        // Ambil konfigurasi tarif ter-update untuk jenis kendaraan ini
        $rate = ParkingRate::where('vehicle_type', $this->vehicleType)->first();

        if (! $rate) {
            $this->addError('vehicleType', 'Tarif untuk jenis kendaraan ini belum dikonfigurasi.');

            return;
        }

        // Simpan transaksi di dalam database transaction agar data tetap konsisten (atomis)
        $transaction = DB::transaction(function () use ($plate, $slot, $rate): ParkingTransaction {
            // Buat record transaksi baru dengan menyimpan snapshot tarif saat ini
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

            // Ubah status slot menjadi ditempati (occupied)
            $slot->update(['status' => 'occupied']);

            return $tx;
        });

        $transaction->load('slot');

        // Menyimpan data transaksi ke array agar bisa di-serialize dengan aman oleh Livewire
        $this->lastTransaction = [
            'id' => $transaction->id,
            'vehicle_plate' => $transaction->vehicle_plate,
            'vehicle_type' => $transaction->vehicle_type,
            'entry_time' => $transaction->entry_time,
            'officer_name' => $transaction->officer_name,
            'slot_code' => $transaction->slot?->slot_code,
        ];

        // Tampilkan karcis tiket untuk dicetak
        $this->showTicket = true;
        unset($this->availableSlots);
    }

    /**
     * Mengatur ulang semua state properti formulir gerbang masuk ke kondisi awal.
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

    /**
     * Merender file view Blade untuk antarmuka Gerbang Masuk.
     */
    public function render(): View
    {
        return view('livewire.entry-gate');
    }
}
