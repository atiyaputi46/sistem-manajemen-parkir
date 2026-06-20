<div>

    {{-- ===== FLASH MESSAGE ===== --}}
    @if (session('rateSuccess'))
        <div class="mb-4 flex items-center gap-3 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('rateSuccess') }}
        </div>
    @endif

    {{-- =========================================== --}}
    {{-- BAGIAN 1: TARIF SAAT INI                   --}}
    {{-- =========================================== --}}
    <div class="mb-8">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800">Tarif Saat Ini</h2>
            <p class="text-sm text-gray-500">Klik Edit untuk mengubah tarif per jenis kendaraan</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kendaraan</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Tarif Jam Pertama</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Tarif Jam Berikutnya</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Tarif Maks Harian</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Denda Karcis Hilang</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Edit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rates as $rate)
                        <tr wire:key="rate-{{ $rate->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-gray-800">
                                <div class="flex items-center gap-2">
                                    @if ($rate->vehicle_type === 'motor')
                                        {{-- Motorcycle icon --}}
                                        <svg class="h-5 w-5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="5.5" cy="17.5" r="2.5"/>
                                            <circle cx="18.5" cy="17.5" r="2.5"/>
                                            <path d="M8 17.5h7"/>
                                            <path d="M14 17.5V12l-3-4H8l-2 2.5"/>
                                            <path d="M14 12h4l1 2"/>
                                            <path d="M10 8V6"/>
                                        </svg>
                                    @elseif ($rate->vehicle_type === 'mobil')
                                        {{-- Car icon --}}
                                        <svg class="h-5 w-5 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 17H3a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1l2-4h12l2 4h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2"/>
                                            <circle cx="7.5" cy="17.5" r="1.5"/>
                                            <circle cx="16.5" cy="17.5" r="1.5"/>
                                            <path d="M5 12h14"/>
                                        </svg>
                                    @else
                                        {{-- Truck icon --}}
                                        <svg class="h-5 w-5 text-orange-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="1" y="3" width="15" height="13" rx="1"/>
                                            <path d="M16 8h4l3 5v4h-7V8z"/>
                                            <circle cx="5.5" cy="18.5" r="1.5"/>
                                            <circle cx="18.5" cy="18.5" r="1.5"/>
                                        </svg>
                                    @endif
                                    <span>{{ ucfirst($rate->vehicle_type) }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right text-sm text-gray-700">Rp {{ number_format($rate->first_hour_rate, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right text-sm text-gray-700">Rp {{ number_format($rate->subsequent_hour_rate, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right text-sm text-gray-700">
                                @if ($rate->daily_max_rate)
                                    Rp {{ number_format($rate->daily_max_rate, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right text-sm text-gray-700">Rp {{ number_format($rate->fine_lost_ticket, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <button wire:click="openEditRateModal({{ $rate->id }})"
                                    class="rounded-md px-3 py-1.5 text-xs font-medium text-indigo-600 ring-1 ring-indigo-200 hover:bg-indigo-50 transition-colors">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">Belum ada data tarif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================================== --}}
    {{-- BAGIAN 2: RIWAYAT PERUBAHAN TARIF           --}}
    {{-- =========================================== --}}
    <div class="mb-8">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800">Riwayat Perubahan Tarif</h2>
            <p class="text-sm text-gray-500">20 perubahan tarif terakhir</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Waktu</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Diubah Oleh</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kendaraan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tarif Lama → Tarif Baru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($changeLogs as $log)
                        <tr wire:key="log-{{ $log->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5 text-gray-800 font-medium">{{ $log->changedBy?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    @if ($log->vehicle_type === 'motor') bg-blue-100 text-blue-700
                                    @elseif ($log->vehicle_type === 'mobil') bg-green-100 text-green-700
                                    @else bg-orange-100 text-orange-700 @endif">
                                    @if ($log->vehicle_type === 'motor')
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="5.5" cy="17.5" r="2.5"/><circle cx="18.5" cy="17.5" r="2.5"/><path d="M8 17.5h7"/><path d="M14 17.5V12l-3-4H8l-2 2.5"/><path d="M14 12h4l1 2"/><path d="M10 8V6"/>
                                        </svg>
                                    @elseif ($log->vehicle_type === 'mobil')
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 17H3a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1l2-4h12l2 4h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/><path d="M5 12h14"/>
                                        </svg>
                                    @else
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v4h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/>
                                        </svg>
                                    @endif
                                    {{ ucfirst($log->vehicle_type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                @php
                                    $old = $log->old_rates;
                                    $new = $log->new_rates;
                                    $fields = [
                                        'first_hour_rate' => 'Jam 1',
                                        'subsequent_hour_rate' => 'Jam +',
                                        'daily_max_rate' => 'Maks',
                                        'fine_lost_ticket' => 'Denda',
                                    ];
                                    $diffs = [];
                                    foreach ($fields as $key => $label) {
                                        $oldVal = $old[$key] ?? null;
                                        $newVal = $new[$key] ?? null;
                                        if ((string) $oldVal !== (string) $newVal) {
                                            $oldFmt = $oldVal !== null ? 'Rp ' . number_format($oldVal, 0, ',', '.') : '—';
                                            $newFmt = $newVal !== null ? 'Rp ' . number_format($newVal, 0, ',', '.') : '—';
                                            $diffs[] = "<span class=\"text-gray-500\">{$label}:</span> {$oldFmt} → <span class=\"font-medium text-gray-800\">{$newFmt}</span>";
                                        }
                                    }
                                @endphp
                                @if (count($diffs) > 0)
                                    <div class="space-y-0.5 text-xs">
                                        @foreach ($diffs as $diff)
                                            <div>{!! $diff !!}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada perubahan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400">Belum ada riwayat perubahan tarif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================================== --}}
    {{-- BAGIAN 3: RIWAYAT TRANSAKSI PEMBAYARAN     --}}
    {{-- =========================================== --}}
    <div>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Riwayat Transaksi Pembayaran</h2>
                <p class="text-sm text-gray-500">Kendaraan yang sudah selesai & dibayar</p>
            </div>
            {{-- Filter Tanggal --}}
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500">Dari</label>
                <input wire:model.live="dateFrom" type="date"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                <label class="text-xs font-medium text-gray-500">Sampai</label>
                <input wire:model.live="dateTo" type="date"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jam Keluar</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Durasi</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Biaya</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Metode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($transactions as $tx)
                            @php
                                $rowNo = ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration;
                                $durationMinutes = $tx->duration_minutes;
                                if ($durationMinutes !== null) {
                                    $hours = (int) floor($durationMinutes / 60);
                                    $mins = $durationMinutes % 60;
                                    $durationStr = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
                                } else {
                                    $durationStr = '—';
                                }
                            @endphp
                            <tr wire:key="tx-{{ $tx->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5 text-gray-400">{{ $rowNo }}</td>
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
                                <td class="px-4 py-3.5 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->entry_time)->format('d/m H:i') }}</td>
                                <td class="px-4 py-3.5 text-gray-600 whitespace-nowrap">{{ $tx->exit_time ? \Carbon\Carbon::parse($tx->exit_time)->format('d/m H:i') : '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $durationStr }}</td>
                                <td class="px-4 py-3.5 text-right font-medium text-gray-800">Rp {{ number_format($tx->fee, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $tx->payment_method ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $tx->officer_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-400">Tidak ada transaksi pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($transactions->hasPages())
                <div class="border-t border-gray-100 px-5 py-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- =========================================== --}}
    {{-- MODAL EDIT TARIF                            --}}
    {{-- =========================================== --}}
    @if ($showEditRateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-800">
                        Edit Tarif — {{ ucfirst($editVehicleType) }}
                    </h3>
                    <button wire:click="closeEditRateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    {{-- Tarif Jam Pertama --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tarif Jam Pertama (Rp) <span class="text-red-500">*</span></label>
                        <input wire:model="editFirstHourRate" type="number" min="0" step="500" placeholder="misal: 3000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editFirstHourRate') border-red-400 @enderror" />
                        @error('editFirstHourRate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Tarif Jam Berikutnya --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tarif Jam Berikutnya (Rp) <span class="text-red-500">*</span></label>
                        <input wire:model="editSubsequentHourRate" type="number" min="0" step="500" placeholder="misal: 2000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editSubsequentHourRate') border-red-400 @enderror" />
                        @error('editSubsequentHourRate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Tarif Maks Harian --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tarif Maks Harian (Rp) <span class="text-xs text-gray-400 font-normal">— opsional</span></label>
                        <input wire:model="editDailyMaxRate" type="number" min="0" step="1000" placeholder="Kosongkan jika tidak ada batas"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editDailyMaxRate') border-red-400 @enderror" />
                        @error('editDailyMaxRate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Denda Karcis Hilang --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Denda Karcis Hilang (Rp) <span class="text-red-500">*</span></label>
                        <input wire:model="editFineLostTicket" type="number" min="0" step="1000" placeholder="misal: 50000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editFineLostTicket') border-red-400 @enderror" />
                        @error('editFineLostTicket') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3">
                        <p class="text-xs text-amber-700">
                            ⚠️ Perubahan tarif hanya berlaku untuk kendaraan yang masuk setelah disimpan. Transaksi yang sedang berjalan tidak terpengaruh.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeEditRateModal"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveRate" wire:loading.attr="disabled"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveRate">Simpan Tarif</span>
                        <span wire:loading wire:target="saveRate">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
