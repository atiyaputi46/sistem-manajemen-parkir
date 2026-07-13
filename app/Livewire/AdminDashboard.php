<?php

namespace App\Livewire;

use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Komponen Livewire untuk Dashboard Admin.
 * Menangani perhitungan statistik real-time seperti pendapatan hari ini,
 * okupansi slot parkir, kendaraan aktif di dalam, dan statistik grafik per jam.
 */
#[Title('Dashboard')]
class AdminDashboard extends Component
{
    /**
     * Menyimpan total pendapatan hari ini (jumlah nominal fee transaksi selesai).
     */
    public float $totalRevenueToday = 0;

    /**
     * Menyimpan jumlah kendaraan yang saat ini sedang parkir (status = parked).
     */
    public int $activeVehicles = 0;

    /**
     * Persentase okupansi kapasitas parkir terisi (slot occupied / total slot).
     */
    public int $occupancyPercent = 0;

    /**
     * Jumlah transaksi yang ditandai bermasalah (status = flagged).
     */
    public int $flaggedCount = 0;

    /**
     * Menyimpan data jumlah kendaraan masuk per jam untuk grafik (24 jam, index 0-23).
     *
     * @var array<int, int>
     */
    public array $chartData = [];

    /**
     * Method inisialisasi awal saat komponen dimuat.
     * Memanggil method loadStats untuk memuat data pertama kali.
     */
    public function mount(): void
    {
        $this->loadStats();
    }

    /**
     * Memuat ulang data statistik parkir dari database.
     * Method ini juga dipicu secara periodik setiap 60 detik melalui wire:poll pada view.
     */
    public function loadStats(): void
    {
        // Menghitung total biaya (fee) dari transaksi berstatus 'exited' pada hari ini
        $this->totalRevenueToday = (float) ParkingTransaction::query()
            ->where('status', 'exited')
            ->whereDate('exit_time', today())
            ->sum('fee');

        // Menghitung jumlah kendaraan yang berstatus 'parked' (masih berada di dalam area parkir)
        $this->activeVehicles = ParkingTransaction::where('status', 'parked')->count();

        // Mengambil jumlah slot yang terisi, total slot, dan menghitung persentase kapasitas terisi
        $occupied = ParkingSlot::where('status', 'occupied')->count();
        $total = ParkingSlot::count();
        $this->occupancyPercent = $total > 0 ? (int) round(($occupied / $total) * 100) : 0;

        // Mengambil jumlah transaksi parkir yang berstatus bermasalah/flagged
        $this->flaggedCount = ParkingTransaction::where('status', 'flagged')->count();

        // Mengambil data mentah jumlah transaksi masuk per jam untuk hari ini dari database
        $rawHourly = ParkingTransaction::query()
            ->selectRaw('HOUR(entry_time) as hour, COUNT(*) as count')
            ->whereDate('entry_time', today())
            ->groupByRaw('HOUR(entry_time)')
            ->pluck('count', 'hour')
            ->toArray();

        // Mengisi array chartData untuk masing-masing jam dari 0 hingga 23
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourly[$i] = $rawHourly[$i] ?? 0;
        }

        $this->chartData = $hourly;
    }

    /**
     * Merender file view Blade yang terkait dengan komponen dashboard admin.
     */
    public function render(): View
    {
        return view('livewire.admin-dashboard');
    }
}
