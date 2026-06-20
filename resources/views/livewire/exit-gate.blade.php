<div wire:poll.10s>

    {{-- Semua konten layar (disembunyikan saat print) --}}
    <div class="max-w-2xl print:hidden">

    {{-- ===== HEADER ===== --}}
    <div class="mb-5 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Exit Gate</h2>
            <p class="text-sm text-gray-500">Proses kendaraan keluar dari area parkir</p>
        </div>
        <div class="text-right text-xs text-gray-400">
            <p class="font-medium text-gray-600">{{ now()->format('d M Y') }}</p>
            <p>{{ now()->format('H:i') }} · {{ auth()->user()->name }}</p>
        </div>
    </div>

    {{-- ===== TOMBOL KARCIS HILANG ===== --}}
    <div class="mb-4 flex justify-end">
        <button
            type="button"
            wire:click="$set('showLostTicketModal', true)"
            class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Karcis Hilang
        </button>
    </div>

    {{-- ===== MAIN CARD ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm divide-y divide-gray-100">

        {{-- ----- BAGIAN 1: PENCARIAN ----- --}}
        <div class="p-6">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">① Cari Kendaraan</p>

            {{-- Toggle mode pencarian --}}
            <div class="mb-4 flex gap-2">
                <button
                    type="button"
                    wire:click="$set('searchMode', 'plate')"
                    class="flex-1 rounded-lg border-2 px-4 py-2 text-sm font-semibold transition-colors
                           {{ $searchMode === 'plate'
                               ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                               : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                    Plat Nomor
                </button>
                <button
                    type="button"
                    wire:click="$set('searchMode', 'id')"
                    class="flex-1 rounded-lg border-2 px-4 py-2 text-sm font-semibold transition-colors
                           {{ $searchMode === 'id'
                               ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                               : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                    Nomor Karcis
                </button>
            </div>

            {{-- Input pencarian --}}
            <div class="flex gap-3">
                <input
                    type="{{ $searchMode === 'id' ? 'number' : 'text' }}"
                    wire:model="searchQuery"
                    placeholder="{{ $searchMode === 'id' ? 'Contoh: 12345' : 'Contoh: B 1234 XYZ' }}"
                    autocomplete="off"
                    autofocus
                    class="block w-full rounded-lg border-gray-300 py-2 px-3 text-sm font-mono uppercase tracking-wider shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500"
                />
                <button
                    type="button"
                    wire:click="findTransaction"
                    wire:loading.attr="disabled"
                    wire:target="findTransaction"
                    class="shrink-0 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white
                           hover:bg-indigo-700 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="findTransaction">Cari Kendaraan</span>
                    <span wire:loading wire:target="findTransaction">Mencari...</span>
                </button>
            </div>

            {{-- Pesan error pencarian --}}
            @if ($errorMessage)
                <div class="mt-3 flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                </div>
            @endif
        </div>

        {{-- ----- BAGIAN 2: DETAIL TRANSAKSI ----- --}}
        @if ($showDetails && $transaction)
            @php
                $now        = \Carbon\Carbon::now();
                $entryTime  = \Carbon\Carbon::parse($transaction['entry_time']);
                $durasi     = (int) $entryTime->diffInMinutes($now);
                $fee        = $this->calculateFee($transaction, $now);
                $fineLost   = $isLostTicket ? (float) $transaction['snapshot_fine_lost_ticket'] : 0;
                $totalFee   = $fee + $fineLost;
            @endphp

            <div class="p-6">
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">② Detail Kendaraan</p>
                    @if ($isLostTicket)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                            ⚠ Karcis Hilang
                        </span>
                    @endif
                </div>

                <div class="space-y-2.5 rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Plat Nomor</span>
                        <span class="text-sm font-bold font-mono tracking-wider text-gray-800">{{ $transaction['vehicle_plate'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Jenis Kendaraan</span>
                        <span class="text-sm font-medium capitalize text-gray-700">{{ $transaction['vehicle_type'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Kode Slot</span>
                        <span class="text-sm font-bold text-gray-800">{{ $transaction['slot_code'] ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Jam Masuk</span>
                        <span class="text-sm font-medium text-gray-700">
                            {{ $entryTime->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Durasi Saat Ini</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $this->formatDuration($durasi) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2.5">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Biaya Parkir</span>
                            <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($fee, 0, ',', '.') }}</span>
                        </div>
                        @if ($isLostTicket)
                            <div class="mt-1.5 flex justify-between">
                                <span class="text-sm text-amber-600">Denda Karcis Hilang</span>
                                <span class="text-sm font-semibold text-amber-700">Rp {{ number_format($fineLost, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="mt-2 flex justify-between border-t border-gray-200 pt-2">
                            <span class="text-sm font-bold text-gray-700">Total Biaya</span>
                            <span class="text-base font-extrabold text-indigo-700">Rp {{ number_format($totalFee, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ----- BAGIAN 3: METODE PEMBAYARAN ----- --}}
            <div class="p-6">
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">③ Metode Pembayaran</p>

                <div class="grid grid-cols-2 gap-3">
                    {{-- Tunai --}}
                    <button
                        type="button"
                        wire:click="$set('paymentMethod', 'tunai')"
                        class="flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 text-sm font-semibold transition-colors
                               {{ $paymentMethod === 'tunai'
                                   ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                   : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}">
                        {{-- Cash / banknotes icon --}}
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M1 10h4M19 10h4M1 14h4M19 14h4"/>
                        </svg>
                        Tunai
                    </button>

                    {{-- Kartu Akses --}}
                    <button
                        type="button"
                        wire:click="$set('paymentMethod', 'kartu_akses')"
                        class="flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 text-sm font-semibold transition-colors
                               {{ $paymentMethod === 'kartu_akses'
                                   ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                   : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}">
                        {{-- Credit card icon --}}
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        Kartu Akses
                    </button>
                </div>
            </div>
        @endif

        {{-- ----- BAGIAN 4: TOMBOL AKSI ----- --}}
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex items-center gap-3">
            <button
                type="button"
                wire:click="resetForm"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50 transition-colors shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset
            </button>

            <button
                type="button"
                wire:click="processExit"
                wire:loading.attr="disabled"
                wire:target="processExit"
                @disabled(!$showDetails || !$transaction || $paymentMethod === '')
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                       hover:bg-green-700 active:bg-green-800 transition-colors
                       disabled:cursor-not-allowed disabled:opacity-40">
                <span wire:loading.remove wire:target="processExit">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <span wire:loading wire:target="processExit">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="processExit">Konfirmasi Pembayaran</span>
                <span wire:loading wire:target="processExit">Memproses...</span>
            </button>
        </div>

    </div>{{-- end main card --}}


    {{-- ===== MODAL STRUK ===== --}}
    @if ($showReceipt && $receiptData)
        {{-- Overlay --}}
        <div class="fixed inset-0 z-40 bg-black/50 print:hidden"></div>

        {{-- Modal --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 print:hidden">
            <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl overflow-hidden">

                {{-- Header hijau sukses --}}
                <div class="bg-green-500 px-6 py-5 text-center">
                    <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-green-100">Pembayaran Berhasil</p>
                    <p class="mt-0.5 text-2xl font-extrabold font-mono tracking-widest text-white">
                        {{ $receiptData['vehicle_plate'] }}
                    </p>
                    <p class="mt-1 text-xs text-green-100">
                        #{{ str_pad($receiptData['id'], 6, '0', STR_PAD_LEFT) }}
                        · <span class="capitalize">{{ $receiptData['vehicle_type'] }}</span>
                    </p>
                </div>

                {{-- Serrated divider --}}
                <div class="relative flex items-center justify-between bg-white px-0">
                    <div class="-ml-3 h-6 w-6 rounded-full bg-gray-100 border border-gray-200"></div>
                    <div class="flex-1 border-t-2 border-dashed border-gray-200 mx-2"></div>
                    <div class="-mr-3 h-6 w-6 rounded-full bg-gray-100 border border-gray-200"></div>
                </div>

                {{-- Detail struk --}}
                <div class="px-6 py-4 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Slot Parkir</span>
                        <span class="text-sm font-bold text-gray-800">{{ $receiptData['slot_code'] ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Jam Masuk</span>
                        <span class="text-sm font-medium text-gray-700">
                            {{ \Carbon\Carbon::parse($receiptData['entry_time'])->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Jam Keluar</span>
                        <span class="text-sm font-medium text-gray-700">
                            {{ \Carbon\Carbon::parse($receiptData['exit_time'])->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Durasi Total</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $this->formatDuration($receiptData['duration_minutes']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Metode Bayar</span>
                        <span class="text-sm font-medium capitalize text-gray-700">
                            {{ str_replace('_', ' ', $receiptData['payment_method']) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Petugas</span>
                        <span class="text-sm font-medium text-gray-700">{{ $receiptData['officer_name'] }}</span>
                    </div>

                    <div class="border-t border-gray-200 pt-2.5 space-y-1.5">
                        @if ($receiptData['is_lost_ticket'])
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Biaya Parkir</span>
                                <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($receiptData['base_fee'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-amber-600">Denda Karcis Hilang</span>
                                <span class="text-sm font-semibold text-amber-700">Rp {{ number_format($receiptData['fine_lost_ticket'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-base font-bold text-gray-700">Total</span>
                            <span class="text-base font-extrabold text-indigo-700">Rp {{ number_format($receiptData['total_fee'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tombol aksi --}}
                <div class="flex gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Struk
                    </button>
                    <button
                        type="button"
                        wire:click="resetForm"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                        Selesai
                    </button>
                </div>

            </div>
        </div>

        {{-- Versi cetak --}}
        </div>{{-- end print:hidden wrapper --}}

        <div class="hidden print:block" style="font-family:'Courier New',Courier,monospace;width:72mm;margin:0 auto;padding:8mm 6mm;font-size:11px;line-height:1.6;color:#000">

            {{-- Header --}}
            <div style="text-align:center;margin-bottom:6mm">
                <p style="font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin:0">Sistem Manajemen Parkir</p>
                <p style="font-size:18px;font-weight:900;letter-spacing:0.12em;text-transform:uppercase;margin:2px 0 0">STRUK KELUAR</p>
            </div>

            <div style="border-top:1px dashed #000;margin-bottom:4mm"></div>

            {{-- Info transaksi --}}
            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="padding:1px 0;width:40%">ID</td>
                    <td style="padding:1px 0">: #{{ str_pad($receiptData['id'], 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Plat</td>
                    <td style="padding:1px 0;font-weight:700">: {{ $receiptData['vehicle_plate'] }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Jenis</td>
                    <td style="padding:1px 0">: {{ ucfirst($receiptData['vehicle_type']) }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Slot</td>
                    <td style="padding:1px 0">: {{ $receiptData['slot_code'] ?? '—' }}</td>
                </tr>
            </table>

            <div style="border-top:1px dashed #000;margin:3mm 0"></div>

            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="padding:1px 0;width:40%">Masuk</td>
                    <td style="padding:1px 0">: {{ \Carbon\Carbon::parse($receiptData['entry_time'])->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Keluar</td>
                    <td style="padding:1px 0">: {{ \Carbon\Carbon::parse($receiptData['exit_time'])->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Durasi</td>
                    <td style="padding:1px 0">: {{ $this->formatDuration($receiptData['duration_minutes']) }}</td>
                </tr>
            </table>

            <div style="border-top:1px dashed #000;margin:3mm 0"></div>

            <table style="width:100%;border-collapse:collapse">
                @if ($receiptData['is_lost_ticket'])
                    <tr>
                        <td style="padding:1px 0;width:55%">Biaya Parkir</td>
                        <td style="padding:1px 0;text-align:right">Rp {{ number_format($receiptData['base_fee'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:1px 0">Denda Karcis Hilang</td>
                        <td style="padding:1px 0;text-align:right">Rp {{ number_format($receiptData['fine_lost_ticket'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top:1px solid #000;padding-top:2px"></td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:1px 0;font-weight:700;font-size:13px">TOTAL</td>
                    <td style="padding:1px 0;text-align:right;font-weight:700;font-size:13px">Rp {{ number_format($receiptData['total_fee'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Metode Bayar</td>
                    <td style="padding:1px 0;text-align:right">{{ ucwords(str_replace('_', ' ', $receiptData['payment_method'])) }}</td>
                </tr>
            </table>

            <div style="border-top:1px dashed #000;margin:3mm 0"></div>

            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="padding:1px 0;width:40%">Petugas</td>
                    <td style="padding:1px 0">: {{ $receiptData['officer_name'] }}</td>
                </tr>
            </table>

            <div style="border-top:1px dashed #000;margin:4mm 0 3mm"></div>

            <p style="text-align:center;font-size:10px;margin:0">Terima kasih atas kunjungan Anda.</p>
            <p style="text-align:center;font-size:10px;margin:1px 0 0">Simpan struk ini sebagai bukti pembayaran.</p>

        </div>
    @endif


    {{-- ===== MODAL KARCIS HILANG ===== --}}
    @if ($showLostTicketModal)
        <div class="fixed inset-0 z-40 bg-black/50"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl overflow-hidden">

                <div class="bg-amber-500 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-white">Karcis Hilang</p>
                            <p class="text-sm text-amber-100">Cari kendaraan berdasarkan plat nomor</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Plat Nomor Kendaraan</label>
                        <input
                            type="text"
                            wire:model="lostTicketPlate"
                            placeholder="Contoh: B 1234 XYZ"
                            autocomplete="off"
                            class="block w-full rounded-lg border-gray-300 py-2 px-3 text-sm font-mono uppercase tracking-wider shadow-sm
                                   focus:border-amber-500 focus:ring-amber-500"
                        />
                    </div>

                    @if ($lostTicketError)
                        <div class="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-700">{{ $lostTicketError }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <button
                        type="button"
                        wire:click="$set('showLostTicketModal', false)"
                        class="flex-1 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="findByPlateForLostTicket"
                        wire:loading.attr="disabled"
                        wire:target="findByPlateForLostTicket"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="findByPlateForLostTicket">Cari Kendaraan</span>
                        <span wire:loading wire:target="findByPlateForLostTicket">Mencari...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    </div>{{-- end max-w-2xl print:hidden --}}

</div>
