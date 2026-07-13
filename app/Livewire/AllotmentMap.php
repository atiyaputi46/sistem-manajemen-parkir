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

/**
 * Komponen Livewire untuk Peta Alokasi Slot Parkir (Allotment Map).
 * Digunakan untuk menampilkan denah slot parkir real-time dengan status warnanya,
 * serta memfasilitasi admin untuk memantau detail kendaraan terparkir dan melakukan status override manual.
 */
#[Title('Allotment Map')]
class AllotmentMap extends Component
{
    /**
     * Filter jenis kendaraan aktif: 'all', 'motor', 'mobil', atau 'truk'.
     */
    public string $filter = 'all';

    /**
     * Menyimpan data slot parkir yang sedang dipilih/diklik untuk ditampilkan di detail modal.
     *
     * @var array<string, mixed>|null
     */
    public ?array $selectedSlot = null;

    /**
     * Menyimpan data transaksi aktif (waktu masuk, plat nomor, durasi) untuk slot yang dipilih.
     *
     * @var array<string, mixed>|null
     */
    public ?array $activeTransaction = null;

    /**
     * Mengambil kumpulan slot parkir dari database berdasarkan filter jenis kendaraan yang aktif.
     *
     * @return Collection<int, ParkingSlot>
     */
    public function parkingSlots(): Collection
    {
        return $this->filter === 'all'
            ? ParkingSlot::orderBy('slot_code')->get()
            : ParkingSlot::where('vehicle_type', $this->filter)->orderBy('slot_code')->get();
    }

    /**
     * Mengatur nilai filter jenis kendaraan dan mereset status pilihan slot yang aktif.
     *
     * @param  string  $value  Jenis kendaraan ('all', 'motor', 'mobil', 'truk')
     */
    public function setFilter(string $value): void
    {
        $this->filter = $value;
        $this->selectedSlot = null;
        $this->activeTransaction = null;
    }

    /**
     * Menangani aksi klik pada slot parkir yang terisi (occupied) untuk memuat info transaksi aktifnya.
     *
     * @param  int  $slotId  ID slot parkir yang dipilih
     */
    public function selectSlot(int $slotId): void
    {
        $slot = ParkingSlot::find($slotId);

        // Hanya tampilkan detail jika slot ditemukan dan statusnya 'occupied' (terisi)
        if (! $slot || $slot->status !== 'occupied') {
            return;
        }

        $this->selectedSlot = $slot->toArray();

        // Cari transaksi parkir aktif terakhir untuk slot tersebut
        $tx = ParkingTransaction::where('slot_id', $slotId)
            ->where('status', 'parked')
            ->latest('entry_time')
            ->first();

        // Jika transaksi aktif ditemukan, hitung durasi parkir berjalan
        if ($tx) {
            $entryTime = Carbon::parse($tx->entry_time);
            $duration = $entryTime->diff(now());
            $durationText = '';

            // Format durasi parkir menjadi jam dan menit
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

    /**
     * Menutup modal tampilan detail slot parkir.
     */
    public function closeModal(): void
    {
        $this->selectedSlot = null;
        $this->activeTransaction = null;
    }

    /**
     * Memfasilitasi admin untuk memaksakan status slot parkir (override) di luar alur sistem normal.
     * Hanya status 'available', 'reserved', dan 'disabled' yang diizinkan untuk di-override.
     *
     * @param  int  $slotId  ID slot parkir yang akan diubah
     * @param  string  $newStatus  Status baru yang ditargetkan
     */
    public function overrideSlotStatus(int $slotId, string $newStatus): void
    {
        $allowed = ['available', 'reserved', 'disabled'];

        // Cek validitas status baru
        if (! in_array($newStatus, $allowed, true)) {
            return;
        }

        $slot = ParkingSlot::find($slotId);

        if (! $slot) {
            return;
        }

        // Perbarui status slot di database
        $slot->update(['status' => $newStatus]);

        // Catat aktivitas tindakan admin ke log keamanan
        Log::info("Admin override slot {$slot->slot_code} to {$newStatus} by ".auth()->user()->name);

        // Jika slot yang diubah statusnya sedang dibuka detailnya di modal, tutup modal tersebut
        if ($this->selectedSlot && (int) $this->selectedSlot['id'] === $slotId) {
            $this->selectedSlot = null;
            $this->activeTransaction = null;
        }

        // Emit event/dispatch untuk memicu re-render reaktif pada grid halaman peta
        $this->dispatch('slot-updated');
    }

    /**
     * Merender file view Blade allotment-map dengan menyuplai daftar slot parkir terfilter.
     */
    public function render(): View
    {
        return view('livewire.allotment-map', [
            'parkingSlots' => $this->parkingSlots(),
        ]);
    }
}
