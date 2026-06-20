<div>

    {{-- Semua konten layar (disembunyikan saat print) --}}
    <div class="max-w-2xl print:hidden">

    {{-- ===== HEADER ===== --}}
    <div class="mb-5 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Entry Gate</h2>
            <p class="text-sm text-gray-500">Catat kendaraan masuk ke area parkir</p>
        </div>
        <div class="text-right text-xs text-gray-400">
            <p class="font-medium text-gray-600">{{ now()->format('d M Y') }}</p>
            <p>{{ now()->format('H:i') }} · {{ auth()->user()->name }}</p>
        </div>
    </div>

    {{-- ===== MAIN CARD ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm divide-y divide-gray-100">

        {{-- ----- BAGIAN 1: PLAT NOMOR ----- --}}
        <div class="p-6">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">① Plat Nomor</p>

            <input
                type="text"
                wire:model.live.debounce.400ms="vehiclePlate"
                placeholder="Contoh: B 1234 XYZ"
                autocomplete="off"
                autofocus
                class="block w-full rounded-lg border-gray-300 py-2 px-3 text-sm font-mono uppercase tracking-wider shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500
                       @error('vehiclePlate') border-red-400 focus:border-red-400 focus:ring-red-400 @enderror"
            />

            @error('vehiclePlate')
                <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                    <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror

            {{-- Badge member aktif --}}
            @if ($activeMember)
                <div class="mt-3 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5">
                    <svg class="h-4 w-4 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium text-green-700">
                        <span class="font-bold">✓ Member Aktif</span> — {{ $activeMember['full_name'] }}
                    </p>
                </div>
            @endif

            {{-- Alert duplikat --}}
            @if ($isDuplicate)
                <div class="mt-3 flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-red-700">Kendaraan sudah tercatat masuk!</p>
                        <p class="text-sm text-red-600">Proses keluar dulu di <strong>Exit Gate</strong>.</p>
                    </div>
                </div>
            @endif
        </div>


        {{-- ----- BAGIAN 2: JENIS KENDARAAN ----- --}}
        <div class="p-6">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">② Jenis Kendaraan</p>

            <div class="grid grid-cols-3 gap-3">
                {{-- Motor --}}
                <button
                    type="button"
                    wire:click="$set('vehicleType', 'motor')"
                    class="flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 text-sm font-semibold transition-colors
                           {{ $vehicleType === 'motor'
                               ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                               : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}">
                    {{-- Motorcycle icon --}}
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="5.5" cy="17.5" r="2.5"/>
                        <circle cx="18.5" cy="17.5" r="2.5"/>
                        <path d="M8 17.5h7"/>
                        <path d="M10 17.5V11l3-4h3l2 4"/>
                        <path d="M7 11h4"/>
                        <path d="M14 7h2.5"/>
                    </svg>
                    Motor
                </button>

                {{-- Mobil --}}
                <button
                    type="button"
                    wire:click="$set('vehicleType', 'mobil')"
                    class="flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 text-sm font-semibold transition-colors
                           {{ $vehicleType === 'mobil'
                               ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                               : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}">
                    {{-- Car icon --}}
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 17H3a2 2 0 01-2-2v-4a2 2 0 011.7-1.97L6.08 8A6 6 0 0111.93 5h.14a6 6 0 015.85 3.03L20.3 9.03A2 2 0 0122 11v4a2 2 0 01-2 2h-2"/>
                        <circle cx="7" cy="17" r="2"/>
                        <circle cx="17" cy="17" r="2"/>
                        <path d="M9 17h6"/>
                    </svg>
                    Mobil
                </button>

                {{-- Truk --}}
                <button
                    type="button"
                    wire:click="$set('vehicleType', 'truk')"
                    class="flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 text-sm font-semibold transition-colors
                           {{ $vehicleType === 'truk'
                               ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                               : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}">
                    {{-- Truck icon --}}
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 3h15v13H1z"/>
                        <path d="M16 8h4l3 3v5h-7V8z"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                    Truk
                </button>
            </div>
        </div>

        {{-- ----- BAGIAN 3: PILIH SLOT ----- --}}
        <div class="p-6" wire:loading.class="opacity-60" wire:target="vehicleType">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">③ Pilih Slot Parkir</p>
                @if ($this->availableSlots->isNotEmpty())
                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                        {{ $this->availableSlots->count() }} tersedia
                    </span>
                @endif
            </div>

            @if ($this->availableSlots->isEmpty())
                <div class="flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                    <svg class="h-4 w-4 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium text-amber-700">Tidak ada slot tersedia untuk kendaraan ini.</p>
                </div>
            @else
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach ($this->availableSlots as $index => $slot)
                        <div class="relative flex flex-col" wire:key="slot-{{ $slot->id }}">

                            {{-- Badge rekomendasi di atas kartu, di luar button --}}
                            @if ($index === 0)
                                <span class="mb-1 self-start whitespace-nowrap rounded-full bg-indigo-500 px-1.5 py-px text-[7px] font-bold uppercase tracking-wide text-white leading-tight">
                                    Rekomendasi
                                </span>
                            @else
                                <span class="mb-1 h-[14px] block"></span>
                            @endif

                            <button
                                type="button"
                                wire:click="selectSlot({{ $slot->id }})"
                                class="flex flex-col items-start rounded-lg border-2 pt-2 pb-2.5 px-3 transition-colors
                                       {{ $selectedSlotId === $slot->id
                                           ? 'border-indigo-500 bg-indigo-50'
                                           : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-bold {{ $selectedSlotId === $slot->id ? 'text-indigo-700' : 'text-gray-800' }}">
                                        {{ $slot->slot_code }}
                                    </p>
                                    @if ($selectedSlotId === $slot->id)
                                        <svg class="h-3.5 w-3.5 shrink-0 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-[10px] text-gray-400">Lt.{{ $slot->floor }}</p>
                            </button>

                        </div>
                    @endforeach
                </div>
            @endif

            @error('selectedSlotId')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>


        {{-- ----- BAGIAN 4: TOMBOL AKSI ----- --}}
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl space-y-3">

            {{-- Ringkasan singkat --}}
            @if (trim($vehiclePlate) !== '' && $selectedSlotId)
                <div class="rounded-lg bg-white border border-gray-200 px-4 py-2.5 flex items-center justify-center gap-4 text-sm">
                    <span>
                        <span class="text-gray-400 text-xs">Plat</span>
                        <span class="ml-1 font-mono font-bold text-gray-800">{{ strtoupper($vehiclePlate) }}</span>
                    </span>
                    <span class="text-gray-300">·</span>
                    <span>
                        <span class="text-gray-400 text-xs">Jenis</span>
                        <span class="ml-1 font-medium text-gray-700 capitalize">{{ $vehicleType }}</span>
                    </span>
                    <span class="text-gray-300">·</span>
                    <span>
                        <span class="text-gray-400 text-xs">Slot</span>
                        <span class="ml-1 font-bold text-gray-800">
                            {{ $this->availableSlots->firstWhere('id', $selectedSlotId)?->slot_code ?? '—' }}
                        </span>
                    </span>
                </div>
            @endif

            <div class="flex items-center gap-3">
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
                    wire:click="confirmEntry"
                    wire:loading.attr="disabled"
                    wire:target="confirmEntry"
                    @disabled($isDuplicate || trim($vehiclePlate) === '' || strlen(trim($vehiclePlate)) < 4 || $selectedSlotId === null)
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-indigo-700 active:bg-indigo-800 transition-colors
                           disabled:cursor-not-allowed disabled:opacity-40">
                    <span wire:loading.remove wire:target="confirmEntry">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="confirmEntry">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="confirmEntry">Proses Masuk</span>
                    <span wire:loading wire:target="confirmEntry">Menyimpan...</span>
                </button>
            </div>

        </div>

    </div>{{-- end main card --}}


    {{-- ===== MODAL KARCIS ===== --}}
    @if ($showTicket && $lastTransaction)
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
                    <p class="text-sm font-medium text-green-100">Transaksi Berhasil</p>
                    <p class="mt-0.5 text-2xl font-extrabold font-mono tracking-widest text-white">
                        {{ $lastTransaction['vehicle_plate'] }}
                    </p>
                    <p class="mt-1 text-xs text-green-100">
                        #{{ str_pad($lastTransaction['id'], 6, '0', STR_PAD_LEFT) }}
                        · <span class="capitalize">{{ $lastTransaction['vehicle_type'] }}</span>
                    </p>
                </div>

                {{-- Garis dashed serrated --}}
                <div class="relative flex items-center justify-between bg-white px-0">
                    <div class="-ml-3 h-6 w-6 rounded-full bg-gray-100 border border-gray-200"></div>
                    <div class="flex-1 border-t-2 border-dashed border-gray-200 mx-2"></div>
                    <div class="-mr-3 h-6 w-6 rounded-full bg-gray-100 border border-gray-200"></div>
                </div>

                {{-- Detail karcis --}}
                <div class="px-6 py-4 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Slot Parkir</span>
                        <span class="text-sm font-bold text-gray-800">{{ $lastTransaction['slot_code'] ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Waktu Masuk</span>
                        <span class="text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($lastTransaction['entry_time'])->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Petugas</span>
                        <span class="text-sm font-medium text-gray-700">{{ $lastTransaction['officer_name'] }}</span>
                    </div>
                    @if ($activeMember)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Status</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                ✓ Member Aktif
                            </span>
                        </div>
                    @endif
                </div>

                <div class="px-6 pb-4 text-center">
                    <p class="text-xs text-gray-400">Simpan karcis ini. Kehilangan karcis dikenakan denda.</p>
                </div>

                {{-- Tombol --}}
                <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 space-y-2">
                    {{-- Baris 1: Cetak + Selesai --}}
                    <div class="flex gap-3">
                        <button
                            type="button"
                            onclick="window.print()"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak
                        </button>
                        <button
                            type="button"
                            wire:click="$set('showTicket', false)"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-green-300 bg-white px-4 py-2 text-sm font-semibold text-green-700 hover:bg-green-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Selesai
                        </button>
                    </div>
                    {{-- Baris 2: Tambah kendaraan berikutnya --}}
                    <button
                        type="button"
                        wire:click="resetForm"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Tambah Kendaraan Masuk
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
                <p style="font-size:18px;font-weight:900;letter-spacing:0.12em;text-transform:uppercase;margin:2px 0 0">KARCIS MASUK</p>
            </div>

            <div style="border-top:1px dashed #000;margin-bottom:4mm"></div>

            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="padding:1px 0;width:40%">ID</td>
                    <td style="padding:1px 0">: #{{ str_pad($lastTransaction['id'], 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Plat</td>
                    <td style="padding:1px 0;font-weight:700">: {{ $lastTransaction['vehicle_plate'] }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Jenis</td>
                    <td style="padding:1px 0">: {{ ucfirst($lastTransaction['vehicle_type']) }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Slot</td>
                    <td style="padding:1px 0">: {{ $lastTransaction['slot_code'] ?? '—' }}</td>
                </tr>
            </table>

            <div style="border-top:1px dashed #000;margin:3mm 0"></div>

            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="padding:1px 0;width:40%">Masuk</td>
                    <td style="padding:1px 0">: {{ \Carbon\Carbon::parse($lastTransaction['entry_time'])->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding:1px 0">Petugas</td>
                    <td style="padding:1px 0">: {{ $lastTransaction['officer_name'] }}</td>
                </tr>
            </table>

            <div style="border-top:1px dashed #000;margin:3mm 0"></div>

            <p style="text-align:center;font-size:10px;margin:0">Simpan karcis ini sebagai bukti masuk.</p>
            <p style="text-align:center;font-size:10px;margin:1px 0 0">Kehilangan karcis dikenakan denda.</p>

        </div>
    @endif

    </div>{{-- end max-w-2xl print:hidden --}}

</div>
