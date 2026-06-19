<?php

namespace App\Livewire;

use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class AdminDashboard extends Component
{
    /** Total pendapatan hari ini (SUM fee, status=exited, exit_time hari ini). */
    public float $totalRevenueToday = 0;

    /** Jumlah kendaraan aktif di dalam (status=parked). */
    public int $activeVehicles = 0;

    /** Persentase kapasitas terisi (occupied / total slot). */
    public int $occupancyPercent = 0;

    /** Jumlah transaksi flagged. */
    public int $flaggedCount = 0;

    /**
     * Data chart 24 elemen (index = jam 0–23).
     *
     * @var array<int, int>
     */
    public array $chartData = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    /**
     * Dipanggil otomatis oleh wire:poll setiap 60 detik.
     */
    public function loadStats(): void
    {
        // a. Total pendapatan hari ini
        $this->totalRevenueToday = (float) ParkingTransaction::query()
            ->where('status', 'exited')
            ->whereDate('exit_time', today())
            ->sum('fee');

        // b. Kendaraan aktif di dalam
        $this->activeVehicles = ParkingTransaction::where('status', 'parked')->count();

        // c. Persentase kapasitas
        $occupied = ParkingSlot::where('status', 'occupied')->count();
        $total = ParkingSlot::count();
        $this->occupancyPercent = $total > 0 ? (int) round(($occupied / $total) * 100) : 0;

        // d. Flagged count
        $this->flaggedCount = ParkingTransaction::where('status', 'flagged')->count();

        // e. Chart data: kendaraan masuk per jam hari ini
        $rawHourly = ParkingTransaction::query()
            ->selectRaw('HOUR(entry_time) as hour, COUNT(*) as count')
            ->whereDate('entry_time', today())
            ->groupByRaw('HOUR(entry_time)')
            ->pluck('count', 'hour')
            ->toArray();

        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourly[$i] = $rawHourly[$i] ?? 0;
        }

        $this->chartData = $hourly;
    }

    public function render(): View
    {
        return view('livewire.admin-dashboard');
    }
}
