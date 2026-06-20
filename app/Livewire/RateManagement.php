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

#[Title('Payments & Rates')]
class RateManagement extends Component
{
    use WithPagination;

    // ===== Modal Edit Tarif =====
    public bool $showEditRateModal = false;

    public ?int $editingRateId = null;

    public string $editVehicleType = '';

    public string $editFirstHourRate = '';

    public string $editSubsequentHourRate = '';

    public string $editDailyMaxRate = '';

    public string $editFineLostTicket = '';

    // ===== Filter transaksi =====
    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(6)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    // ===== Edit Tarif =====

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

    public function closeEditRateModal(): void
    {
        $this->showEditRateModal = false;
        $this->editingRateId = null;
        $this->resetErrorBag();
    }

    public function saveRate(): void
    {
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

        $old = [
            'first_hour_rate' => $rate->first_hour_rate,
            'subsequent_hour_rate' => $rate->subsequent_hour_rate,
            'daily_max_rate' => $rate->daily_max_rate,
            'fine_lost_ticket' => $rate->fine_lost_ticket,
        ];

        $new = [
            'first_hour_rate' => (float) $this->editFirstHourRate,
            'subsequent_hour_rate' => (float) $this->editSubsequentHourRate,
            'daily_max_rate' => $this->editDailyMaxRate !== '' ? (float) $this->editDailyMaxRate : null,
            'fine_lost_ticket' => (float) $this->editFineLostTicket,
        ];

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

    public function render(): View
    {
        $rates = ParkingRate::orderByRaw("FIELD(vehicle_type, 'motor', 'mobil', 'truk')")->get();

        $changeLogs = RateChangeLog::with('changedBy')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

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
