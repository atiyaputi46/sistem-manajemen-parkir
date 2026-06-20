<?php

namespace App\Console\Commands;

use App\Models\ParkingTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FlagStaleTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parking:flag-stale-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flag parking transactions that have been active for more than 72 hours';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $threshold = now()->subHours(72);

        $stale = ParkingTransaction::where('status', 'parked')
            ->where('entry_time', '<', $threshold)
            ->get();

        $count = $stale->count();

        if ($count > 0) {
            ParkingTransaction::whereIn('id', $stale->pluck('id'))
                ->update(['status' => 'flagged']);

            Log::warning("FlagStaleTransactions: flagged {$count} transactions older than 72 hours.");

            $this->info("Flagged {$count} stale transactions.");
        } else {
            $this->info('No stale transactions found.');
        }
    }
}
