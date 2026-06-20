<?php

namespace App\Http\Controllers;

use App\Exports\ParkingReportExport;
use App\Models\ParkingTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function exportExcel(Request $request): StreamedResponse
    {
        [$start, $end, $label] = $this->resolveDateRange($request);

        $transactions = ParkingTransaction::where('status', 'exited')
            ->whereBetween('exit_time', [$start, $end])
            ->orderBy('exit_time', 'asc')
            ->get();

        $filename = 'laporan-parkir-'.$label.'.xlsx';

        return (new ParkingReportExport($transactions, $label))->download($filename);
    }

    public function exportPdf(Request $request): Response
    {
        [$start, $end, $label] = $this->resolveDateRange($request);

        $transactions = ParkingTransaction::where('status', 'exited')
            ->whereBetween('exit_time', [$start, $end])
            ->orderBy('exit_time', 'asc')
            ->get();

        $totalFee = $transactions->sum('fee');
        $breakdown = [
            'motor' => ['count' => 0, 'fee' => 0],
            'mobil' => ['count' => 0, 'fee' => 0],
            'truk' => ['count' => 0, 'fee' => 0],
        ];

        foreach ($transactions as $tx) {
            if (isset($breakdown[$tx->vehicle_type])) {
                $breakdown[$tx->vehicle_type]['count']++;
                $breakdown[$tx->vehicle_type]['fee'] += (float) $tx->fee;
            }
        }

        $pdf = Pdf::loadView('exports.report-pdf', [
            'transactions' => $transactions,
            'periodLabel' => $label,
            'totalFee' => $totalFee,
            'breakdown' => $breakdown,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-parkir-'.$label.'.pdf');
    }

    /**
     * Resolves date range from request params.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function resolveDateRange(Request $request): array
    {
        $periodType = $request->query('period_type', 'daily');

        if ($periodType === 'daily') {
            $date = $request->query('date', now()->format('Y-m-d'));
            $start = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $end = Carbon::parse($date)->endOfDay()->toDateTimeString();
            $label = $date;
        } elseif ($periodType === 'weekly') {
            $weekEnd = $request->query('week_end_date', now()->format('Y-m-d'));
            $endDt = Carbon::parse($weekEnd)->endOfDay();
            $startDt = $endDt->copy()->subDays(6)->startOfDay();
            $start = $startDt->toDateTimeString();
            $end = $endDt->toDateTimeString();
            $label = $startDt->format('Y-m-d').'_sd_'.$endDt->format('Y-m-d');
        } else {
            $month = (int) $request->query('month', now()->month);
            $year = (int) $request->query('year', now()->year);
            $startDt = Carbon::createFromDate($year, $month, 1)->startOfMonth()->startOfDay();
            $endDt = $startDt->copy()->endOfMonth()->endOfDay();
            $start = $startDt->toDateTimeString();
            $end = $endDt->toDateTimeString();
            $label = $startDt->format('Y-m');
        }

        return [$start, $end, $label];
    }
}
