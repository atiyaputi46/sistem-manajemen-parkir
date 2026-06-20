<?php

namespace App\Livewire;

use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan')]
class ReportPage extends Component
{
    /** @var 'daily'|'weekly'|'monthly' */
    public string $periodType = 'daily';

    /** Untuk filter harian: YYYY-MM-DD */
    public string $selectedDate = '';

    /** Untuk filter mingguan: tanggal akhir minggu (YYYY-MM-DD) */
    public string $weekEndDate = '';

    /** Untuk filter bulanan */
    public int $selectedMonth = 1;

    public int $selectedYear = 2024;

    /** @var Collection<int, ParkingTransaction> */
    public Collection $transactions;

    public bool $hasLoaded = false;

    public function mount(): void
    {
        $now = Carbon::now();
        $this->selectedDate = $now->format('Y-m-d');
        $this->weekEndDate = $now->format('Y-m-d');
        $this->selectedMonth = (int) $now->format('n');
        $this->selectedYear = (int) $now->format('Y');
        $this->transactions = new Collection;
    }

    public function setPeriodType(string $type): void
    {
        $this->periodType = $type;
        $this->transactions = new Collection;
        $this->hasLoaded = false;
    }

    public function loadReport(): void
    {
        [$startDatetime, $endDatetime] = $this->resolveDateRange();

        $this->transactions = ParkingTransaction::where('status', 'exited')
            ->whereBetween('exit_time', [$startDatetime, $endDatetime])
            ->orderBy('exit_time', 'asc')
            ->get();

        $this->hasLoaded = true;
    }

    /**
     * Mengembalikan [$startDatetime, $endDatetime] berdasarkan $periodType.
     *
     * @return array{0: string, 1: string}
     */
    public function resolveDateRange(): array
    {
        if ($this->periodType === 'daily') {
            $start = Carbon::parse($this->selectedDate)->startOfDay();
            $end = Carbon::parse($this->selectedDate)->endOfDay();
        } elseif ($this->periodType === 'weekly') {
            $end = Carbon::parse($this->weekEndDate)->endOfDay();
            $start = $end->copy()->subDays(6)->startOfDay();
        } else {
            $start = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();
        }

        return [$start->toDateTimeString(), $end->toDateTimeString()];
    }

    /**
     * Label periode untuk nama file ekspor.
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
     * Params untuk dikirim ke URL ekspor via anchor tag.
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

    public function render(): View
    {
        return view('livewire.report-page');
    }
}
