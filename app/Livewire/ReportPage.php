<?php

namespace App\Livewire;

use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Komponen Livewire untuk Halaman Laporan.
 * Menangani filter data transaksi selesai berdasarkan jenis periode (harian, mingguan, bulanan),
 * pemuatan daftar transaksi terkait, dan penyediaan parameter untuk ekspor file laporan (Excel/PDF).
 */
#[Title('Laporan')]
class ReportPage extends Component
{
    /**
     * Tipe periode filter aktif: 'daily', 'weekly', atau 'monthly'.
     *
     * @var 'daily'|'weekly'|'monthly'
     */
    public string $periodType = 'daily';

    /**
     * Menyimpan nilai tanggal untuk filter harian (YYYY-MM-DD).
     */
    public string $selectedDate = '';

    /**
     * Menyimpan nilai tanggal akhir minggu untuk filter mingguan (YYYY-MM-DD).
     */
    public string $weekEndDate = '';

    /**
     * Menyimpan nilai indeks bulan untuk filter bulanan (1 - 12).
     */
    public int $selectedMonth = 1;

    /**
     * Menyimpan nilai tahun untuk filter bulanan (misal: 2026).
     */
    public int $selectedYear = 2024;

    /**
     * Menyimpan kumpulan transaksi hasil pencarian/filter laporan.
     *
     * @var Collection<int, ParkingTransaction>
     */
    public Collection $transactions;

    /**
     * Flag status penanda apakah laporan sudah pernah dimuat.
     */
    public bool $hasLoaded = false;

    /**
     * Inisialisasi awal properti filter tanggal dengan waktu saat ini.
     */
    public function mount(): void
    {
        $now = Carbon::now();
        $this->selectedDate = $now->format('Y-m-d');
        $this->weekEndDate = $now->format('Y-m-d');
        $this->selectedMonth = (int) $now->format('n');
        $this->selectedYear = (int) $now->format('Y');
        $this->transactions = new Collection;
    }

    /**
     * Mengatur tipe periode aktif dan mereset hasil pencarian sebelumnya.
     *
     * @param  string  $type  Tipe periode ('daily', 'weekly', 'monthly')
     */
    public function setPeriodType(string $type): void
    {
        $this->periodType = $type;
        $this->transactions = new Collection;
        $this->hasLoaded = false;
    }

    /**
     * Memuat daftar transaksi selesai (status = exited) dari database sesuai dengan range tanggal terfilter.
     */
    public function loadReport(): void
    {
        // Pecah range tanggal berdasarkan tipe periode
        [$startDatetime, $endDatetime] = $this->resolveDateRange();

        // Cari transaksi keluar dalam rentang tanggal tersebut
        $this->transactions = ParkingTransaction::where('status', 'exited')
            ->whereBetween('exit_time', [$startDatetime, $endDatetime])
            ->orderBy('exit_time', 'asc')
            ->get();

        $this->hasLoaded = true;
    }

    /**
     * Mengembalikan array format string tanggal awal dan tanggal akhir [$startDatetime, $endDatetime]
     * berdasarkan konfigurasi tipe periode dan input filter pengguna.
     *
     * @return array{0: string, 1: string}
     */
    public function resolveDateRange(): array
    {
        if ($this->periodType === 'daily') {
            // Harian: dari jam 00:00:00 hingga 23:59:59 pada hari tersebut
            $start = Carbon::parse($this->selectedDate)->startOfDay();
            $end = Carbon::parse($this->selectedDate)->endOfDay();
        } elseif ($this->periodType === 'weekly') {
            // Mingguan: 7 hari ke belakang dari tanggal akhir minggu terpilih
            $end = Carbon::parse($this->weekEndDate)->endOfDay();
            $start = $end->copy()->subDays(6)->startOfDay();
        } else {
            // Bulanan: dari tanggal 1 jam 00:00:00 hingga akhir bulan terkait jam 23:59:59
            $start = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();
        }

        return [$start->toDateTimeString(), $end->toDateTimeString()];
    }

    /**
     * Mengembalikan teks label representatif periode untuk penamaan file hasil ekspor.
     */
    public function periodLabel(): string
    {
        if ($this->periodType === 'daily') {
            return $this->selectedDate;
        }

        if ($this->periodType === 'weekly') {
            $end = Carbon::parse($this->weekEndDate);
            $start = $end->copy()->subDays(6);

            return $start->format('Y-m-d').'_sd_'.$end->format('Y-m-d');
        }

        return Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->format('Y-m');
    }

    /**
     * Menyusun parameter filter ke dalam array untuk dikirimkan sebagai query string pada URL ekspor Excel/PDF.
     *
     * @return array<string, string|int>
     */
    public function exportParams(): array
    {
        return [
            'period_type' => $this->periodType,
            'date' => $this->selectedDate,
            'week_end_date' => $this->weekEndDate,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
        ];
    }

    /**
     * Merender file view Blade report-page.
     */
    public function render(): View
    {
        return view('livewire.report-page');
    }
}
