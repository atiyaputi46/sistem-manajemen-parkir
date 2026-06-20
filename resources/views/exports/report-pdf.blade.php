<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Parkir — {{ $periodLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

        .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #1e3a5f; letter-spacing: 0.5px; }
        .header h2 { font-size: 13px; font-weight: normal; color: #444; margin-top: 4px; }
        .header .meta { font-size: 10px; color: #777; margin-top: 6px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background-color: #1e3a5f; color: #fff; }
        thead th { padding: 7px 8px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
        tbody td { padding: 6px 8px; font-size: 10px; color: #374151; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }

        .empty-row td { text-align: center; color: #9ca3af; padding: 20px; }

        .summary { margin-top: 8px; }
        .summary h3 { font-size: 12px; font-weight: bold; color: #1e3a5f; margin-bottom: 8px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        .summary-grid { display: table; width: 100%; }
        .summary-row { display: table-row; }
        .summary-cell { display: table-cell; padding: 4px 8px; font-size: 11px; vertical-align: top; }
        .summary-label { color: #6b7280; width: 40%; }
        .summary-value { font-weight: bold; color: #111827; }

        .breakdown-table { width: 45%; margin-top: 10px; }
        .breakdown-table thead { background-color: #374151; }
        .breakdown-table thead th { font-size: 9px; }
        .breakdown-table tbody td { font-size: 10px; }

        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <h1>SISTEM MANAJEMEN PARKIR</h1>
        <h2>Laporan Pendapatan Parkir</h2>
        <p class="meta">
            Periode: <strong>{{ $periodLabel }}</strong>
            &nbsp;|&nbsp;
            Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </p>
    </div>

    {{-- ===== TABEL TRANSAKSI ===== --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:12%">Plat Nomor</th>
                <th style="width:11%">Jenis</th>
                <th style="width:14%">Jam Masuk</th>
                <th style="width:14%">Jam Keluar</th>
                <th style="width:9%; text-align:center">Durasi</th>
                <th style="width:13%; text-align:right">Biaya (Rp)</th>
                <th style="width:11%">Metode</th>
                <th style="width:12%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $tx)
                @php
                    $dur = $tx->duration_minutes;
                    if ($dur !== null) {
                        $h = (int) floor($dur / 60);
                        $m = $dur % 60;
                        $durStr = $h > 0 ? "{$h}j {$m}m" : "{$m}m";
                    } else {
                        $durStr = '—';
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $tx->vehicle_plate }}</strong></td>
                    <td>{{ ucfirst($tx->vehicle_type) }}</td>
                    <td>{{ $tx->entry_time ? \Carbon\Carbon::parse($tx->entry_time)->format('d/m/Y H:i') : '—' }}</td>
                    <td>{{ $tx->exit_time  ? \Carbon\Carbon::parse($tx->exit_time)->format('d/m/Y H:i') : '—' }}</td>
                    <td class="center">{{ $durStr }}</td>
                    <td class="right">{{ number_format((float) $tx->fee, 0, ',', '.') }}</td>
                    <td>{{ $tx->payment_method ?? '—' }}</td>
                    <td>{{ $tx->officer_name ?? '—' }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="9">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== RINGKASAN ===== --}}
    <div class="summary">
        <h3>Ringkasan</h3>

        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Kendaraan</div>
                <div class="summary-cell summary-value">{{ $transactions->count() }} kendaraan</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Pendapatan</div>
                <div class="summary-cell summary-value">Rp {{ number_format((float) $totalFee, 0, ',', '.') }}</div>
            </div>
        </div>

        <br>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Jenis Kendaraan</th>
                    <th style="text-align:center">Jumlah</th>
                    <th style="text-align:right">Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($breakdown as $type => $data)
                    <tr>
                        <td>{{ ucfirst($type) }}</td>
                        <td class="center">{{ $data['count'] }}</td>
                        <td class="right">{{ number_format((float) $data['fee'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Sistem Manajemen Parkir — Laporan ini digenerate otomatis oleh sistem
    </div>

</body>
</html>
