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

#[Title('Exit Gate')]
class ExitGate extends Component
{
    // ── Pencarian ──────────────────────────────────────────────────
    public string $searchMode = 'plate'; // 'id' | 'plate'

    public string $searchQuery = '';

    public ?string $errorMessage = null;

    // ── Transaksi ditemukan ────────────────────────────────────────
    public bool $showDetails = false;

    /** @var array<string, mixed>|null */
    public ?array $transaction = null;

    public bool $isLostTicket = false;

    // ── Pembayaran ─────────────────────────────────────────────────
    public string $paymentMethod = '';

    // ── Struk ──────────────────────────────────────────────────────
    public bool $showReceipt = false;

    /** @var array<string, mixed>|null */
    public ?array $receiptData = null;

    // ── Modal Karcis Hilang ────────────────────────────────────────
    public bool $showLostTicketModal = false;

    public string $lostTicketPlate = '';

    public ?string $lostTicketError = null;

    // ──────────────────────────────────────────────────────────────

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

        $tx = $this->searchMode === 'id'
            ? ParkingTransaction::with('slot')
                ->where('id', $query)
                ->where('status', 'parked')
                ->first()
            : ParkingTransaction::with('slot')
                ->where('vehicle_plate', strtoupper($query))
                ->where('status', 'parked')
                ->first();

        if (! $tx) {
            $this->errorMessage = 'Transaksi tidak ditemukan atau kendaraan sudah keluar.';

            return;
        }

        $this->transaction = $this->transactionToArray($tx);
        $this->showDetails = true;
    }

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

        $this->transaction = $this->transactionToArray($tx);
        $this->showDetails = true;
        $this->isLostTicket = true;
        $this->showLostTicketModal = false;
        $this->lostTicketPlate = '';
        $this->errorMessage = null;
        $this->paymentMethod = '';
    }

    public function processExit(): void
    {
        if (! $this->transaction || $this->paymentMethod === '') {
            return;
        }

        $now = now();
        $baseFee = $this->calculateFee($this->transaction, $now);
        $fineLostTicket = $this->isLostTicket ? (float) $this->transaction['snapshot_fine_lost_ticket'] : 0;
        $totalFee = $baseFee + $fineLostTicket;

        $transactionId = $this->transaction['id'];
        $slotId = $this->transaction['slot_id'];

        DB::transaction(function () use ($transactionId, $slotId, $now, $totalFee): void {
            ParkingTransaction::where('id', $transactionId)->update([
                'exit_time' => $now,
                'fee' => $totalFee,
                'payment_method' => $this->paymentMethod,
                'status' => 'exited',
            ]);

            ParkingSlot::where('id', $slotId)->update(['status' => 'available']);
        });

        $entryTime = Carbon::parse($this->transaction['entry_time']);
        $durationMinutes = (int) $entryTime->diffInMinutes($now);

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

        $this->showReceipt = true;
    }

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
     * Hitung biaya parkir berdasarkan snapshot tarif pada transaksi.
     *
     * @param  array<string, mixed>  $transaction
     */
    public function calculateFee(array $transaction, ?Carbon $now = null): float
    {
        $now = $now ?? now();
        $durationMinutes = (int) Carbon::parse($transaction['entry_time'])->diffInMinutes($now);

        if ($durationMinutes <= 60) {
            $fee = (float) $transaction['snapshot_first_hour_rate'];
        } else {
            $additionalHours = (int) ceil(($durationMinutes - 60) / 60);
            $fee = (float) $transaction['snapshot_first_hour_rate']
                + ($additionalHours * (float) $transaction['snapshot_subsequent_hour_rate']);
        }

        if (
            $transaction['snapshot_daily_max_rate'] !== null
            && $fee > (float) $transaction['snapshot_daily_max_rate']
        ) {
            $fee = (float) $transaction['snapshot_daily_max_rate'];
        }

        return $fee;
    }

    /**
     * Format menit ke "X jam Y menit".
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
     * Konversi ParkingTransaction ke array plain agar bisa di-serialize Livewire.
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

    public function render(): View
    {
        return view('livewire.exit-gate');
    }
}
