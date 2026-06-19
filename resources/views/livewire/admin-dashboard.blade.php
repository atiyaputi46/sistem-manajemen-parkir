<div wire:poll.60s="loadStats">

    {{-- ===== HEADER ===== --}}
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Dashboard Overview</h2>
            <p class="text-sm text-gray-500">Ringkasan kondisi parkir hari ini · Auto-refresh setiap 60 detik</p>
        </div>
        <div class="text-right text-xs text-gray-400">
            <p class="font-medium text-gray-600">{{ now()->format('d M Y') }}</p>
            <p>{{ now()->format('H:i') }}</p>
        </div>
    </div>

    {{-- ===== ALERT FLAGGED ===== --}}
    @if ($flaggedCount > 0)
        <div class="mb-6 flex items-center gap-3 rounded-lg border border-red-300 bg-red-50 px-5 py-3.5">
            <span class="text-lg">⚠</span>
            <p class="flex-1 text-sm font-medium text-red-700">
                Ada <strong>{{ $flaggedCount }}</strong> kendaraan dengan transaksi bermasalah (&gt;72 jam).
                Cek halaman Allotment.
            </p>
            <a href="{{ route('allotment') }}"
               class="shrink-0 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700 transition-colors">
                Cek Allotment →
            </a>
        </div>
    @endif

    {{-- ===== WIDGET RINGKASAN ===== --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

        {{-- Widget: Total Pendapatan --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Pendapatan Hari Ini</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-gray-800">
                Rp {{ number_format($totalRevenueToday, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-gray-400">Dari transaksi yang sudah selesai hari ini</p>
        </div>

        {{-- Widget: Kendaraan Aktif --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Kendaraan di Dalam</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-gray-800">{{ $activeVehicles }}</p>
            <p class="mt-1 text-xs text-gray-400">Kendaraan dengan status terparkir aktif</p>
        </div>

        {{-- Widget: Kapasitas Terisi --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Kapasitas Terisi</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-gray-800">{{ $occupancyPercent }}%</p>
            {{-- Progress bar --}}
            <div class="mt-3 h-2.5 w-full rounded-full bg-gray-100">
                <div class="h-2.5 rounded-full transition-all duration-500
                    @if ($occupancyPercent >= 90) bg-red-500
                    @elseif ($occupancyPercent >= 60) bg-yellow-400
                    @else bg-green-500
                    @endif"
                    style="width: {{ $occupancyPercent }}%">
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-400">Slot occupied dari total slot tersedia</p>
        </div>

    </div>

    {{-- ===== GRAFIK BAR ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-700">Kendaraan Masuk Per Jam</p>
                <p class="text-xs text-gray-400">Hari ini, jam 00:00 – 23:00</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">
                Total: {{ array_sum($chartData) }} kendaraan
            </span>
        </div>
        <div class="relative h-64">
            <canvas id="hourlyChart"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const chartData = @json(array_values($chartData));
    const labels    = Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0') + ':00');

    const ctx = document.getElementById('hourlyChart');
    if (!ctx) { return; }

    // Hancurkan instance lama jika ada (Livewire re-render / poll)
    if (window._hourlyChart instanceof Chart) {
        window._hourlyChart.destroy();
    }

    window._hourlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kendaraan Masuk',
                data: chartData,
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderColor: 'rgb(99, 102, 241)',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => 'Jam ' + items[0].label,
                        label: (item) => ' ' + item.raw + ' kendaraan',
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, maxRotation: 0 }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        font: { size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });

    // Rebuild chart setiap Livewire selesai update (wire:poll)
    document.addEventListener('livewire:updated', function () {
        const fresh = @json(array_values($chartData));
        if (window._hourlyChart instanceof Chart) {
            window._hourlyChart.data.datasets[0].data = fresh;
            window._hourlyChart.update();
        }
    });
}());
</script>
@endpush
