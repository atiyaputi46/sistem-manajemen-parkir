<div>

    {{-- ===== HEADER ===== --}}
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Laporan Pendapatan</h2>
            <p class="text-sm text-gray-500">Filter dan ekspor laporan transaksi parkir</p>
        </div>
    </div>

    {{-- ===== FILTER CARD ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm mb-6">

        {{-- Toggle Periode --}}
        <div class="mb-5">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Periode</p>
            <div class="flex gap-2">
                <button wire:click="setPeriodType('daily')"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition-colors
                        {{ $periodType === 'daily' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Harian
                </button>
                <button wire:click="setPeriodType('weekly')"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition-colors
                        {{ $periodType === 'weekly' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Mingguan
                </button>
                <button wire:click="setPeriodType('monthly')"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition-colors
                        {{ $periodType === 'monthly' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Bulanan
                </button>
            </div>
        </div>

        {{-- Input sesuai periodType --}}
        <div class="flex flex-wrap items-end gap-4">

            @if ($periodType === 'daily')
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Pilih Tanggal</label>
                    <input wire:model="selectedDate" type="date"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                </div>

            @elseif ($periodType === 'weekly')
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Tanggal Akhir Minggu</label>
                    <input wire:model="weekEndDate" type="date"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    @php
                        $endParsed   = \Carbon\Carbon::parse($weekEndDate);
                        $startParsed = $endParsed->copy()->subDays(6);
                    @endphp
                    <p class="mt-1 text-xs text-gray-400">
                        Rentang: {{ $startParsed->format('d/m/Y') }} — {{ $endParsed->format('d/m/Y') }}
                    </p>
                </div>

            @else
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Bulan</label>
                    <select wire:model="selectedMonth"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                            <option value="{{ $i + 1 }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Tahun</label>
                    <input wire:model="selectedYear" type="number" min="2020" max="2099" step="1"
                        class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                </div>
            @endif

            <button wire:click="loadReport" wire:loading.attr="disabled"
                class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors disabled:opacity-60 flex items-center gap-2">
                <span wire:loading.remove wire:target="loadReport">
                    <svg class="inline-block h-4 w-4 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Tampilkan Laporan
                </span>
                <span wire:loading wire:target="loadReport">Memuat...</span>
            </button>
        </div>
    </div>

    {{-- ===== HASIL LAPORAN (hanya tampil setelah loadReport dipanggil) ===== --}}
    @if ($hasLoaded)
        {{-- Tombol Ekspor --}}
        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm font-medium text-gray-700">
                Periode: <span class="text-indigo-600 font-semibold">{{ $this->periodLabel() }}</span>
                &nbsp;·&nbsp;
                {{ $transactions->count() }} transaksi ditemukan
            </p>
            @if ($transactions->isNotEmpty())
                @php
                    $params = $this->exportParams();
                @endphp
                <div class="flex gap-2">
                    <a href="{{ route('report.export.excel', $params) }}" target="_blank"
                        class="flex items-center gap-1.5 rounded-lg border border-green-600 bg-green-50 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Ekspor Excel
                    </a>
                    <a href="{{ route('report.export.pdf', $params) }}" target="_blank"
                        class="flex items-center gap-1.5 rounded-lg border border-red-500 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Ekspor PDF
                    </a>
                </div>
            @endif
        </div>

        {{-- Tabel Laporan --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Plat Nomor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kendaraan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jam Keluar</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Durasi</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Biaya</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Metode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($transactions as $index => $tx)
                            @php
                                $dur = $tx->duration_minutes;
                                if ($dur !== null) {
                                    $h      = (int) floor($dur / 60);
                                    $m      = $dur % 60;
                                    $durStr = $h > 0 ? "{$h}j {$m}m" : "{$m}m";
                                } else {
                                    $durStr = '—';
                                }
                            @endphp
                            <tr wire:key="report-tx-{{ $tx->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3.5 font-mono font-semibold text-gray-800">{{ $tx->vehicle_plate }}</td>
                                <td class="px-4 py-3.5 text-gray-600">
                                    <div class="flex items-center gap-1.5">
                                        @if ($tx->vehicle_type === 'motor')
                                            <svg class="h-4 w-4 text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="5.5" cy="17.5" r="2.5"/><circle cx="18.5" cy="17.5" r="2.5"/><path d="M8 17.5h7"/><path d="M14 17.5V12l-3-4H8l-2 2.5"/><path d="M14 12h4l1 2"/><path d="M10 8V6"/>
                                            </svg>
                                        @elseif ($tx->vehicle_type === 'mobil')
                                            <svg class="h-4 w-4 text-green-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 17H3a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1l2-4h12l2 4h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/><path d="M5 12h14"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-orange-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v4h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/>
                                            </svg>
                                        @endif
                                        {{ ucfirst($tx->vehicle_type) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-gray-600 whitespace-nowrap">
                                    {{ $tx->entry_time ? \Carbon\Carbon::parse($tx->entry_time)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-gray-600 whitespace-nowrap">
                                    {{ $tx->exit_time ? \Carbon\Carbon::parse($tx->exit_time)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-center text-gray-600">{{ $durStr }}</td>
                                <td class="px-4 py-3.5 text-right font-medium text-gray-800">
                                    Rp {{ number_format((float) $tx->fee, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $tx->payment_method ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $tx->officer_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-sm text-gray-400">
                                    Tidak ada transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== RINGKASAN ===== --}}
        @if ($transactions->isNotEmpty())
            @php
                $totalFee  = $transactions->sum('fee');
                $breakdown = [
                    'motor' => ['count' => 0, 'fee' => 0.0],
                    'mobil' => ['count' => 0, 'fee' => 0.0],
                    'truk'  => ['count' => 0, 'fee' => 0.0],
                ];
                foreach ($transactions as $tx) {
                    if (isset($breakdown[$tx->vehicle_type])) {
                        $breakdown[$tx->vehicle_type]['count']++;
                        $breakdown[$tx->vehicle_type]['fee'] += (float) $tx->fee;
                    }
                }
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-bold text-gray-800 uppercase tracking-wider">Ringkasan Periode</h3>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-5">
                    <div class="rounded-lg bg-indigo-50 border border-indigo-100 p-4">
                        <p class="text-xs text-indigo-500 font-medium">Total Kendaraan</p>
                        <p class="mt-1 text-2xl font-extrabold text-indigo-700">{{ $transactions->count() }}</p>
                    </div>
                    <div class="rounded-lg bg-green-50 border border-green-100 p-4 sm:col-span-3">
                        <p class="text-xs text-green-600 font-medium">Total Pendapatan</p>
                        <p class="mt-1 text-2xl font-extrabold text-green-700">
                            Rp {{ number_format((float) $totalFee, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Breakdown per Jenis Kendaraan</p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ($breakdown as $type => $data)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                @if ($type === 'motor')
                                    <svg class="h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="5.5" cy="17.5" r="2.5"/><circle cx="18.5" cy="17.5" r="2.5"/><path d="M8 17.5h7"/><path d="M14 17.5V12l-3-4H8l-2 2.5"/><path d="M14 12h4l1 2"/><path d="M10 8V6"/>
                                    </svg>
                                @elseif ($type === 'mobil')
                                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 17H3a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1l2-4h12l2 4h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/><path d="M5 12h14"/>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v4h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/>
                                    </svg>
                                @endif
                                <span class="text-sm font-semibold text-gray-700">{{ ucfirst($type) }}</span>
                            </div>
                            <p class="text-xl font-bold text-gray-800">{{ $data['count'] }} <span class="text-sm font-normal text-gray-500">kendaraan</span></p>
                            <p class="mt-1 text-sm font-medium text-gray-600">Rp {{ number_format((float) $data['fee'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

</div>
