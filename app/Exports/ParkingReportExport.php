<?php

namespace App\Exports;

use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParkingReportExport
{
    /**
     * @param  Collection<int, ParkingTransaction>  $transactions
     */
    public function __construct(
        private readonly Collection $transactions,
        private readonly string $periodLabel
    ) {}

    public function download(string $filename): StreamedResponse
    {
        return response()->streamDownload(function () {
            $options = new Options;
            $writer = new Writer($options);
            $writer->openToFile('php://output');

            // --- Header row ---
            $headerStyle = (new Style)->setFontBold();
            $writer->addRow(Row::fromValues([
                'No',
                'Plat Nomor',
                'Jenis Kendaraan',
                'Jam Masuk',
                'Jam Keluar',
                'Durasi (menit)',
                'Biaya (Rp)',
                'Metode Pembayaran',
                'Petugas',
            ], $headerStyle));

            // --- Data rows ---
            $totalFee = 0;
            $breakdownData = ['motor' => ['count' => 0, 'fee' => 0], 'mobil' => ['count' => 0, 'fee' => 0], 'truk' => ['count' => 0, 'fee' => 0]];
            $no = 1;

            foreach ($this->transactions as $tx) {
                $fee = (float) $tx->fee;
                $totalFee += $fee;

                if (isset($breakdownData[$tx->vehicle_type])) {
                    $breakdownData[$tx->vehicle_type]['count']++;
                    $breakdownData[$tx->vehicle_type]['fee'] += $fee;
                }

                $writer->addRow(Row::fromValues([
                    $no++,
                    $tx->vehicle_plate,
                    ucfirst($tx->vehicle_type),
                    $tx->entry_time ? Carbon::parse($tx->entry_time)->format('d/m/Y H:i') : '-',
                    $tx->exit_time ? Carbon::parse($tx->exit_time)->format('d/m/Y H:i') : '-',
                    $tx->duration_minutes ?? '-',
                    $fee,
                    $tx->payment_method ?? '-',
                    $tx->officer_name ?? '-',
                ]));
            }

            // --- Spacer ---
            $writer->addRow(Row::fromValues([]));

            // --- Summary rows ---
            $boldStyle = (new Style)->setFontBold();
            $writer->addRow(Row::fromValues(['RINGKASAN LAPORAN'], $boldStyle));
            $writer->addRow(Row::fromValues(['Periode', $this->periodLabel]));
            $writer->addRow(Row::fromValues(['Total Kendaraan', $this->transactions->count()]));
            $writer->addRow(Row::fromValues(['Total Pendapatan (Rp)', $totalFee]));

            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Breakdown per Jenis Kendaraan'], $boldStyle));
            $writer->addRow(Row::fromValues(['Jenis', 'Jumlah Kendaraan', 'Total Pendapatan (Rp)'], $boldStyle));

            foreach ($breakdownData as $type => $data) {
                $writer->addRow(Row::fromValues([ucfirst($type), $data['count'], $data['fee']]));
            }

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
