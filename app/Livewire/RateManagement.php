<?php

namespace App\Livewire;

use App\Models\ParkingRate;
use App\Models\ParkingTransaction;
use App\Models\RateChangeLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Komponen Livewire untuk Manajemen Tarif dan Pembayaran.
 * Menangani tampilan daftar tarif aktif, edit nominal tarif per jenis kendaraan,
 * pencatatan riwayat perubahan tarif ke log audit, dan melihat riwayat pembayaran transaksi parkir selesai.
 */
#[Title('Payments & Rates')]
class RateManagement extends Component
{
    use WithPagination;

    // ===== Modal Edit Tarif =====

    /**
     * Flag status penampilan modal formulir edit tarif parkir.
     */
    public bool $showEditRateModal = false;

    /**
     * ID tarif kendaraan yang sedang diedit.
     */
    public ?int $editingRateId = null;

    /**
     * Jenis kendaraan yang diedit tarifnya (motor/mobil/truk).
     */
    public string $editVehicleType = '';

    /**
     * Nilai tarif jam pertama yang diinput dalam modal edit.
     */
    public string $editFirstHourRate = '';

    /**
     * Nilai tarif jam berikutnya yang diinput dalam modal edit.
     */
    public string $editSubsequentHourRate = '';

    /**
     * Nilai batas biaya maksimal harian yang diinput dalam modal edit.
     */
    public string $editDailyMaxRate = '';

    /**
     * Nilai denda karcis hilang yang diinput dalam modal edit.
     */
    public string $editFineLostTicket = '';

    // ===== Filter transaksi =====

    /**
     * Tanggal awal filter riwayat transaksi keluar (YYYY-MM-DD).
     */
    public string $dateFrom = '';

    /**
     * Tanggal akhir filter riwayat transaksi keluar (YYYY-MM-DD).
     */
    public string $dateTo = '';

    /**
     * Inisialisasi awal properti filter rentang tanggal (default 7 hari ke belakang).
     */
    public function mount(): void
    {
        $this->dateFrom = now()->subDays(6)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    /**
     * Dipanggil saat filter tanggal awal diubah, mereset pagination kembali ke halaman 1.
     */
    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    /**
     * Dipanggil saat filter tanggal akhir diubah, mereset pagination kembali ke halaman 1.
     */
    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    // ===== Edit Tarif =====

    /**
     * Membuka modal edit tarif dengan memuat data tarif kendaraan aktif saat ini ke input form.
     *
     * @param  int  $rateId  ID tarif yang akan diedit
     */
    public function openEditRateModal(int $rateId): void
    {
        $rate = ParkingRate::findOrFail($rateId);

        $this->editingRateId = $rate->id;
        $this->editVehicleType = $rate->vehicle_type;
        $this->editFirstHourRate = (string) $rate->first_hour_rate;
        $this->editSubsequentHourRate = (string) $rate->subsequent_hour_rate;
        $this->editDailyMaxRate = (string) ($rate->daily_max_rate ?? '');
        $this->editFineLostTicket = (string) $rate->fine_lost_ticket;
        $this->resetErrorBag();

        $this->showEditRateModal = true;
    }

    /**
     * Menutup modal edit tarif dan membersihkan pesan kesalahan validasi.
     */
    public function closeEditRateModal(): void
    {
        $this->showEditRateModal = false;
        $this->editingRateId = null;
        $this->resetErrorBag();
    }

    /**
     * Memvalidasi input nominal tarif baru, memperbarui record di database,
     * serta mencatat riwayat perbandingan tarif lama dan baru ke log audit dalam satu transaksi database.
     */
    public function saveRate(): void
    {
        // Validasi format angka dan keharusan pengisian nilai tarif
        $this->validate([
            'editFirstHourRate' => ['required', 'numeric', 'min:0'],
            'editSubsequentHourRate' => ['required', 'numeric', 'min:0'],
            'editDailyMaxRate' => ['nullable', 'numeric', 'min:0'],
            'editFineLostTicket' => ['required', 'numeric', 'min:0'],
        ], [
            'editFirstHourRate.required' => 'Tarif jam pertama wajib diisi.',
            'editFirstHourRate.numeric' => 'Harus berupa angka.',
            'editSubsequentHourRate.required' => 'Tarif jam berikutnya wajib diisi.',
            'editSubsequentHourRate.numeric' => 'Harus berupa angka.',
            'editDailyMaxRate.numeric' => 'Harus berupa angka.',
            'editFineLostTicket.required' => 'Denda karcis hilang wajib diisi.',
            'editFineLostTicket.numeric' => 'Harus berupa angka.',
        ]);

        $rate = ParkingRate::findOrFail($this->editingRateId);

        // Rekam data tarif lama sebelum di-update
        $old = [
            'first_hour_rate' => $rate->first_hour_rate,
            'subsequent_hour_rate' => $rate->subsequent_hour_rate,
            'daily_max_rate' => $rate->daily_max_rate,
            'fine_lost_ticket' => $rate->fine_lost_ticket,
        ];

        // Susun data tarif baru yang akan diterapkan
        $new = [
            'first_hour_rate' => (float) $this->editFirstHourRate,
            'subsequent_hour_rate' => (float) $this->editSubsequentHourRate,
            'daily_max_rate' => $this->editDailyMaxRate !== '' ? (float) $this->editDailyMaxRate : null,
            'fine_lost_ticket' => (float) $this->editFineLostTicket,
        ];

        // Eksekusi pembaruan dan pencatatan log
        DB::transaction(function () use ($rate, $old, $new): void {
            $rate->update([
                'first_hour_rate' => $new['first_hour_rate'],
                'subsequent_hour_rate' => $new['subsequent_hour_rate'],
                'daily_max_rate' => $new['daily_max_rate'],
                'fine_lost_ticket' => $new['fine_lost_ticket'],
            ]);

            RateChangeLog::create([
                'vehicle_type' => $rate->vehicle_type,
                'changed_by' => Auth::id(),
                'old_rates' => $old,
                'new_rates' => $new,
                'created_at' => now(),
            ]);
        });

        $this->closeEditRateModal();
        session()->flash('rateSuccess', 'Tarif berhasil diperbarui. Berlaku untuk kendaraan yang masuk berikutnya.');
    }

    /**
     * Merender file view Blade rate-management dengan menyajikan data tarif aktif,
     * log riwayat perubahan tarif admin, dan tabel daftar transaksi keluar terfilter.
     */
    public function render(): View
    {
        $rates = ParkingRate::orderByRaw("FIELD(vehicle_type, 'motor', 'mobil', 'truk')")->get();

        // Ambil 20 log perubahan tarif terakhir dari admin
        $changeLogs = RateChangeLog::with('changedBy')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Ambil data transaksi keluar terpaginasi (limit 20) dengan filter rentang tanggal keluar
        $transactions = ParkingTransaction::query()
            ->where('status', 'exited')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('exit_time', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('exit_time', '<=', $this->dateTo))
            ->orderBy('exit_time', 'desc')
            ->paginate(20);

        return view('livewire.rate-management', [
            'rates' => $rates,
            'changeLogs' => $changeLogs,
            'transactions' => $transactions,
        ]);
    }
}
