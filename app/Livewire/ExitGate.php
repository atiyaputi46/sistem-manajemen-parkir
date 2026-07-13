<?php

namespace App\Livewire;

use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Komponen Livewire untuk Gerbang Keluar (Exit Gate POS).
 * Menangani pencarian karcis aktif berdasarkan kode karcis atau plat nomor,
 * penanganan karcis hilang (lost ticket) beserta denda, perhitungan tarif parkir dinamis,
 * pemrosesan pembayaran, dan pencetakan struk pembayaran.
 */
#[Title('Exit Gate')]
class ExitGate extends Component
{
    // ── Pencarian ──────────────────────────────────────────────────

    /**
     * Mode pencarian transaksi: 'id' (Nomor Karcis) atau 'plate' (Plat Nomor).
     */
    public string $searchMode = 'plate';

    /**
     * Kata kunci/query pencarian yang dimasukkan petugas.
     */
    public string $searchQuery = '';

    /**
     * Menyimpan pesan error jika pencarian atau pemrosesan gagal.
     */
    public ?string $errorMessage = null;

    // ── Transaksi ditemukan ────────────────────────────────────────

    /**
     * Flag penanda untuk memunculkan detail transaksi di layar.
     */
    public bool $showDetails = false;

    /**
     * Menyimpan data transaksi parkir aktif yang berhasil ditemukan.
     *
     * @var array<string, mixed>|null
     */
    public ?array $transaction = null;

    /**
     * Flag penanda apakah proses keluar ini menggunakan kasus karcis hilang.
     */
    public bool $isLostTicket = false;

    // ── Pembayaran ─────────────────────────────────────────────────

    /**
     * Metode pembayaran yang dipilih: 'cash', 'e-wallet', 'debit', dll.
     */
    public string $paymentMethod = '';

    // ── Struk ──────────────────────────────────────────────────────

    /**
     * Flag penanda untuk memunculkan modal struk pembayaran setelah transaksi sukses.
     */
    public bool $showReceipt = false;

    /**
     * Menyimpan rincian data struk pembayaran yang siap dicetak.
     *
     * @var array<string, mixed>|null
     */
    public ?array $receiptData = null;

    // ── Modal Karcis Hilang ────────────────────────────────────────

    /**
     * Status penampilan modal pencarian khusus karcis hilang.
     */
    public bool $showLostTicketModal = false;

    /**
     * Nomor plat kendaraan untuk pencarian khusus karcis hilang.
     */
    public string $lostTicketPlate = '';

    /**
     * Menyimpan pesan error khusus untuk pencarian karcis hilang.
     */
    public ?string $lostTicketError = null;

    // ──────────────────────────────────────────────────────────────

    /**
     * Mencari transaksi kendaraan parkir yang aktif berdasarkan kriteria pencarian yang aktif.
     */
    public function findTransaction(): void
    {
        $this->errorMessage = null;
        $this->transaction = null;
        $this->showDetails = false;
        $this->isLostTicket = false;
        $this->paymentMethod = '';

        $query = trim($this->searchQuery);

        if ($query === '') {
            $this->errorMessage = 'Masukkan nomor karcis atau plat nomor terlebih dahulu.';

            return;
        }

        // Cari transaksi dengan memuat relasi slot
        $tx = $this->searchMode === 'id'
            ? ParkingTransaction::with('slot')
                ->where('id', $query)
                ->where('status', 'parked')
                ->first()
            : ParkingTransaction::with('slot')
                ->where('vehicle_plate', strtoupper($query))
                ->where('status', 'parked')
                ->first();

        // Tampilkan pesan error jika data transaksi tidak ada atau kendaraan sudah keluar
        if (! $tx) {
            $this->errorMessage = 'Transaksi tidak ditemukan atau kendaraan sudah keluar.';

            return;
        }

        // Konversi model ke array plain dan tampilkan detail transaksi
        $this->transaction = $this->transactionToArray($tx);
        $this->showDetails = true;
    }

    /**
     * Mencari transaksi parkir aktif berdasarkan nomor plat kendaraan khusus untuk kasus karcis hilang.
     */
    public function findByPlateForLostTicket(): void
    {
        $this->lostTicketError = null;
        $plate = strtoupper(trim($this->lostTicketPlate));

        if ($plate === '') {
            $this->lostTicketError = 'Masukkan plat nomor terlebih dahulu.';

            return;
        }

        $tx = ParkingTransaction::with('slot')
            ->where('vehicle_plate', $plate)
            ->where('status', 'parked')
            ->first();

        if (! $tx) {
            $this->lostTicketError = 'Transaksi tidak ditemukan. Hubungi admin.';

            return;
        }

        // Tampilkan detail transaksi, tandai sebagai kasus karcis hilang, dan tutup modal karcis hilang
        $this->transaction = $this->transactionToArray($tx);
        $this->showDetails = true;
        $this->isLostTicket = true;
        $this->showLostTicketModal = false;
        $this->lostTicketPlate = '';
        $this->errorMessage = null;
        $this->paymentMethod = '';
    }

    /**
     * Memproses penyelesaian transaksi keluar kendaraan, menghitung biaya akhir (ditambah denda jika ada),
     * memperbarui data transaksi di database, membebaskan slot parkir kembali kosong, dan mempersiapkan cetak struk.
     */
    public function processExit(): void
    {
        // Pastikan transaksi aktif sudah dimuat dan petugas telah memilih metode pembayaran
        if (! $this->transaction || $this->paymentMethod === '') {
            return;
        }

        $now = now();
        // Hitung biaya parkir reguler berdasarkan snapshot tarif yang di-lock saat masuk
        $baseFee = $this->calculateFee($this->transaction, $now);
        // Tambahkan denda jika karcis dilaporkan hilang
        $fineLostTicket = $this->isLostTicket ? (float) $this->transaction['snapshot_fine_lost_ticket'] : 0;
        $totalFee = $baseFee + $fineLostTicket;

        $transactionId = $this->transaction['id'];
        $slotId = $this->transaction['slot_id'];

        // Lakukan pembaharuan database di dalam transaction block
        DB::transaction(function () use ($transactionId, $slotId, $now, $totalFee): void {
            // Perbarui waktu keluar, nominal biaya, metode pembayaran, dan status transaksi
            ParkingTransaction::where('id', $transactionId)->update([
                'exit_time' => $now,
                'fee' => $totalFee,
                'payment_method' => $this->paymentMethod,
                'status' => 'exited',
            ]);

            // Ubah status slot parkir terkait kembali menjadi kosong (available)
            ParkingSlot::where('id', $slotId)->update(['status' => 'available']);
        });

        $entryTime = Carbon::parse($this->transaction['entry_time']);
        $durationMinutes = (int) $entryTime->diffInMinutes($now);

        // Rekapitulasi data untuk kebutuhan struk cetak fisik
        $this->receiptData = [
            'id' => $transactionId,
            'vehicle_plate' => $this->transaction['vehicle_plate'],
            'vehicle_type' => $this->transaction['vehicle_type'],
            'slot_code' => $this->transaction['slot_code'],
            'entry_time' => $this->transaction['entry_time'],
            'exit_time' => $now->toDateTimeString(),
            'duration_minutes' => $durationMinutes,
            'base_fee' => $baseFee,
            'fine_lost_ticket' => $fineLostTicket,
            'total_fee' => $totalFee,
            'payment_method' => $this->paymentMethod,
            'officer_name' => Auth::user()->name,
            'is_lost_ticket' => $this->isLostTicket,
        ];

        // Tampilkan modal struk pembayaran
        $this->showReceipt = true;
    }

    /**
     * Mereset seluruh nilai properti form POS keluar ke kondisi awal/kosong.
     */
    public function resetForm(): void
    {
        $this->searchMode = 'plate';
        $this->searchQuery = '';
        $this->errorMessage = null;
        $this->showDetails = false;
        $this->transaction = null;
        $this->isLostTicket = false;
        $this->paymentMethod = '';
        $this->showReceipt = false;
        $this->receiptData = null;
        $this->showLostTicketModal = false;
        $this->lostTicketPlate = '';
        $this->lostTicketError = null;
    }

    /**
     * Menghitung total biaya parkir reguler berjalan berdasarkan snapshot tarif pada transaksi.
     * Menggunakan pembulatan ke atas untuk durasi parkir lebih dari 1 jam.
     *
     * @param  array<string, mixed>  $transaction  Data transaksi parkir
     * @param  \DateTimeInterface|null  $now  Waktu keluar (jika null, default saat ini)
     * @return float Total biaya parkir reguler
     */
    public function calculateFee(array $transaction, ?\DateTimeInterface $now = null): float
    {
        $now = $now !== null ? Carbon::instance($now) : now();
        $durationMinutes = (int) Carbon::parse($transaction['entry_time'])->diffInMinutes($now);

        // Jika durasi parkir di bawah atau sama dengan 60 menit (1 jam pertama)
        if ($durationMinutes <= 60) {
            $fee = (float) $transaction['snapshot_first_hour_rate'];
        } else {
            // Durasi parkir sisa dibagi per 60 menit dan dibulatkan ke atas
            $additionalHours = (int) ceil(($durationMinutes - 60) / 60);
            $fee = (float) $transaction['snapshot_first_hour_rate']
                + ($additionalHours * (float) $transaction['snapshot_subsequent_hour_rate']);
        }

        // Terapkan batas tarif maksimal harian jika terkonfigurasi dan nominal biaya melebihinya
        if (
            $transaction['snapshot_daily_max_rate'] !== null
            && $fee > (float) $transaction['snapshot_daily_max_rate']
        ) {
            $fee = (float) $transaction['snapshot_daily_max_rate'];
        }

        return $fee;
    }

    /**
     * Memformat durasi parkir dalam menit ke teks yang lebih representatif (misal: "2 jam 15 menit").
     *
     * @param  int  $minutes  Durasi parkir dalam satuan menit
     * @return string Representasi durasi parkir berbentuk teks
     */
    public function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} jam";
        }

        return "{$hours} jam {$remainingMinutes} menit";
    }

    /**
     * Mengonversi instance model ParkingTransaction menjadi array polos.
     * Mempermudah serialisasi data oleh Livewire agar terhindar dari state out-of-sync.
     *
     * @param  ParkingTransaction  $tx  Instance model transaksi
     * @return array<string, mixed> Array representasi data transaksi
     */
    private function transactionToArray(ParkingTransaction $tx): array
    {
        return [
            'id' => $tx->id,
            'slot_id' => $tx->slot_id,
            'vehicle_plate' => $tx->vehicle_plate,
            'vehicle_type' => $tx->vehicle_type,
            'entry_time' => $tx->entry_time instanceof Carbon
                ? $tx->entry_time->toDateTimeString()
                : (string) $tx->entry_time,
            'slot_code' => $tx->slot?->slot_code,
            'snapshot_first_hour_rate' => $tx->snapshot_first_hour_rate,
            'snapshot_subsequent_hour_rate' => $tx->snapshot_subsequent_hour_rate,
            'snapshot_daily_max_rate' => $tx->snapshot_daily_max_rate,
            'snapshot_fine_lost_ticket' => $tx->snapshot_fine_lost_ticket,
            'officer_name' => $tx->officer_name,
        ];
    }

    /**
     * Merender file view Blade untuk antarmuka Gerbang Keluar.
     */
    public function render(): View
    {
        return view('livewire.exit-gate');
    }
}
