<?php

namespace App\Http\Controllers;

use App\Exports\ParkingReportExport;
use App\Models\ParkingTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller untuk menangani ekspor data laporan parkir.
 * Menyediakan fungsionalitas unduhan laporan transaksi dalam format Excel (menggunakan Laravel Excel)
 * dan format PDF (menggunakan Barryvdh DomPDF) lengkap dengan kalkulasi rekapitulasi data.
 */
class ReportExportController extends Controller
{
    /**
     * Menangani ekspor data transaksi keluar berstatus 'exited' ke file spreadsheet Excel (XLSX).
     *
     * @param  Request  $request  Request HTTP yang membawa parameter filter tanggal
     * @return StreamedResponse File unduhan spreadsheet
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        // Mendapatkan rentang tanggal awal, akhir, dan label periode
        [$start, $end, $label] = $this->resolveDateRange($request);

        // Cari transaksi keluar dalam rentang tanggal tersebut
        $transactions = ParkingTransaction::where('status', 'exited')
            ->whereBetween('exit_time', [$start, $end])
            ->orderBy('exit_time', 'asc')
            ->get();

        $filename = 'laporan-parkir-'.$label.'.xlsx';

        // Unduh file menggunakan class ParkingReportExport
        return (new ParkingReportExport($transactions, $label))->download($filename);
    }

    /**
     * Menangani ekspor data transaksi keluar berstatus 'exited' ke file dokumen PDF.
     * Melakukan kalkulasi total nominal fee dan rekap jumlah unit serta pendapatan per jenis kendaraan.
     *
     * @param  Request  $request  Request HTTP yang membawa parameter filter tanggal
     * @return Response Dokumen PDF yang di-stream/unduh
     */
    public function exportPdf(Request $request): Response
    {
        // Mendapatkan rentang tanggal awal, akhir, dan label periode
        [$start, $end, $label] = $this->resolveDateRange($request);

        // Cari transaksi keluar dalam rentang tanggal tersebut
        $transactions = ParkingTransaction::where('status', 'exited')
            ->whereBetween('exit_time', [$start, $end])
            ->orderBy('exit_time', 'asc')
            ->get();

        // Hitung total nominal pendapatan bersih
        $totalFee = $transactions->sum('fee');

        // Susun rekapitulasi jumlah kendaraan dan nominal per jenis kendaraan
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

        // Muat halaman template HTML untuk dikonversi ke PDF dengan orientasi lanskap A4
        $pdf = Pdf::loadView('exports.report-pdf', [
            'transactions' => $transactions,
            'periodLabel' => $label,
            'totalFee' => $totalFee,
            'breakdown' => $breakdown,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-parkir-'.$label.'.pdf');
    }

    /**
     * Mengurai request query parameter untuk menentukan rentang tanggal awal,
     * tanggal akhir, dan label penamaan file ekspor berdasarkan jenis periode (daily/weekly/monthly).
     *
     * @param  Request  $request  Request HTTP yang membawa parameter query
     * @return array{0: string, 1: string, 2: string} Array berisi start date, end date, dan label periode
     */
    private function resolveDateRange(Request $request): array
    {
        $periodType = $request->query('period_type', 'daily');

        if ($periodType === 'daily') {
            // Harian: 00:00:00 s.d 23:59:59 pada hari tersebut
            $date = $request->query('date', now()->format('Y-m-d'));
            $start = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $end = Carbon::parse($date)->endOfDay()->toDateTimeString();
            $label = $date;
        } elseif ($periodType === 'weekly') {
            // Mingguan: 7 hari ke belakang dari tanggal akhir minggu terpilih
            $weekEnd = $request->query('week_end_date', now()->format('Y-m-d'));
            $endDt = Carbon::parse($weekEnd)->endOfDay();
            $startDt = $endDt->copy()->subDays(6)->startOfDay();
            $start = $startDt->toDateTimeString();
            $end = $endDt->toDateTimeString();
            $label = $startDt->format('Y-m-d').'_sd_'.$endDt->format('Y-m-d');
        } else {
            // Bulanan: tanggal 1 s.d akhir bulan jam 23:59:59
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
