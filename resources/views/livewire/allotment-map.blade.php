<div wire:poll.10s>

    {{-- ===== HEADER ===== --}}
    <div class="mb-5 flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Allotment Map</h2>
            <p class="text-sm text-gray-500">Peta status slot parkir · Auto-refresh setiap 10 detik</p>
        </div>
        <div class="text-right text-xs text-gray-400">
            <p class="font-medium text-gray-600">{{ now()->format('d M Y') }}</p>
            <p>{{ now()->format('H:i:s') }}</p>
        </div>
    </div>

    {{-- ===== LEGENDA ===== --}}
    <div class="mb-5 flex flex-wrap items-center gap-4 text-xs font-medium text-gray-600">
        <span class="flex items-center gap-1.5">
            <span class="inline-block h-3.5 w-3.5 rounded" style="background:#e5e7eb;border:1px solid #d1d5db"></span>
            Tersedia
        </span>
        <span class="flex items-center gap-1.5">
            <span class="inline-block h-3.5 w-3.5 rounded" style="background:#f87171;border:1px solid #fca5a5"></span>
            Terisi
        </span>
        <span class="flex items-center gap-1.5">
            <span class="inline-block h-3.5 w-3.5 rounded" style="background:#fde047;border:1px solid #fbbf24"></span>
            Reserved
        </span>
        <span class="flex items-center gap-1.5">
            <span class="inline-block h-3.5 w-3.5 rounded" style="background:#1e3a8a;border:1px solid #1e40af"></span>
            Disabled
        </span>
    </div>

    {{-- ===== FILTER TAB ===== --}}
    <div class="mb-5 flex flex-wrap gap-2">
        <button type="button" wire:click="setFilter('all')"
            class="rounded-lg border px-4 py-1.5 text-sm font-medium transition-colors"
            style="{{ $filter === 'all' ? 'background:#4f46e5;border-color:#4f46e5;color:#fff' : 'background:#fff;border-color:#d1d5db;color:#374151' }}">
            Semua
        </button>
        <button type="button" wire:click="setFilter('motor')"
            class="rounded-lg border px-4 py-1.5 text-sm font-medium transition-colors"
            style="{{ $filter === 'motor' ? 'background:#4f46e5;border-color:#4f46e5;color:#fff' : 'background:#fff;border-color:#d1d5db;color:#374151' }}">
            🛵 Motor
        </button>
        <button type="button" wire:click="setFilter('mobil')"
            class="rounded-lg border px-4 py-1.5 text-sm font-medium transition-colors"
            style="{{ $filter === 'mobil' ? 'background:#4f46e5;border-color:#4f46e5;color:#fff' : 'background:#fff;border-color:#d1d5db;color:#374151' }}">
            🚗 Mobil
        </button>
        <button type="button" wire:click="setFilter('truk')"
            class="rounded-lg border px-4 py-1.5 text-sm font-medium transition-colors"
            style="{{ $filter === 'truk' ? 'background:#4f46e5;border-color:#4f46e5;color:#fff' : 'background:#fff;border-color:#d1d5db;color:#374151' }}">
            🚛 Truk
        </button>

        <span class="ml-auto self-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-500">
            {{ $parkingSlots->count() }} slot ditampilkan
        </span>
    </div>

    {{-- ===== GRID SLOT ===== --}}
    @if ($parkingSlots->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center text-sm text-gray-400">
            Tidak ada slot untuk filter ini.
        </div>
    @else
        {{-- Grid pakai inline style agar tidak dipurge Tailwind --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(72px,1fr));gap:8px">
            @foreach ($parkingSlots as $slot)
                @php
                    $icon = match ($slot->vehicle_type) {
                        'motor' => '🛵',
                        'truk'  => '🚛',
                        default => '🚗',
                    };

                    // Inline style per status — aman dari Tailwind purge
                    $cellStyle = match ($slot->status) {
                        'available' => 'background:#f3f4f6;border:2px solid #e5e7eb;color:#4b5563;cursor:default',
                        'occupied'  => 'background:#f87171;border:2px solid #fca5a5;color:#fff;cursor:pointer',
                        'reserved'  => 'background:#fef08a;border:2px solid #fde047;color:#854d0e;cursor:default',
                        'disabled'  => 'background:#1e3a8a;border:2px solid #1e40af;color:#fff;cursor:default',
                        default     => 'background:#f3f4f6;border:2px solid #e5e7eb;color:#4b5563;cursor:default',
                    };
                @endphp

                <div
                    wire:key="slot-{{ $slot->id }}"
                    class="relative rounded-lg p-2 text-center text-xs font-bold select-none group"
                    style="{{ $cellStyle }}"
                    @if ($slot->status === 'occupied')
                        wire:click="selectSlot({{ $slot->id }})"
                    @endif
                >
                    {{-- Kode slot --}}
                    <p style="{{ $slot->status === 'disabled' ? 'text-decoration:line-through;opacity:0.6' : '' }}">
                        {{ $slot->slot_code }}
                    </p>

                    {{-- Ikon kendaraan untuk occupied --}}
                    @if ($slot->status === 'occupied')
                        <p style="margin-top:2px;font-size:1rem;line-height:1">{{ $icon }}</p>
                    @endif

                    {{-- Tombol override "⋮" — muncul saat hover --}}
                    <div class="absolute right-0.5 top-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div x-data="{ open: false }" class="relative">
                            <button
                                type="button"
                                x-on:click.stop="open = !open"
                                style="width:18px;height:18px;display:flex;align-items:center;justify-content:center;border-radius:4px;font-size:10px;font-weight:bold;
                                       {{ in_array($slot->status, ['occupied', 'disabled']) ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:rgba(0,0,0,0.08);color:#374151' }}">
                                ⋮
                            </button>

                            {{-- Dropdown override --}}
                            <div
                                x-show="open"
                                x-on:click.outside="open = false"
                                x-transition
                                class="absolute right-0 top-6 z-20 rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                                style="display:none;min-width:130px"
                            >
                                <p class="border-b border-gray-100 px-3 pb-1 pt-1.5 text-gray-400"
                                   style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em">
                                    Set Status
                                </p>

                                @if ($slot->status !== 'available')
                                    <button type="button"
                                        wire:click="overrideSlotStatus({{ $slot->id }}, 'available')"
                                        x-on:click="open = false"
                                        class="block w-full px-3 py-1.5 text-left text-xs font-medium hover:bg-green-50"
                                        style="color:#15803d">
                                        ✓ Tersedia
                                    </button>
                                @endif

                                @if ($slot->status !== 'reserved')
                                    <button type="button"
                                        wire:click="overrideSlotStatus({{ $slot->id }}, 'reserved')"
                                        x-on:click="open = false"
                                        class="block w-full px-3 py-1.5 text-left text-xs font-medium hover:bg-yellow-50"
                                        style="color:#854d0e">
                                        ⏳ Reserved
                                    </button>
                                @endif

                                @if ($slot->status !== 'disabled')
                                    <button type="button"
                                        wire:click="overrideSlotStatus({{ $slot->id }}, 'disabled')"
                                        x-on:click="open = false"
                                        class="block w-full px-3 py-1.5 text-left text-xs font-medium hover:bg-blue-50"
                                        style="color:#1d4ed8">
                                        ✕ Disable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    {{-- ===== MODAL DETAIL SLOT OCCUPIED ===== --}}
    @if ($selectedSlot)
        <div class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5)" wire:click="closeModal"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-sm rounded-xl bg-white shadow-2xl overflow-hidden">

                {{-- Header merah --}}
                <div style="background:#ef4444" class="px-6 py-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color:#fecaca">Slot Terisi</p>
                            <p class="mt-1 text-2xl font-extrabold font-mono tracking-widest text-white">
                                {{ $selectedSlot['slot_code'] }}
                            </p>
                        </div>
                        <button type="button" wire:click="closeModal"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-white transition-colors"
                            style="background:rgba(255,255,255,0.2)">
                            ✕
                        </button>
                    </div>
                    <p class="mt-1 text-sm capitalize" style="color:#fecaca">
                        @php
                            $typeIcon = match ($selectedSlot['vehicle_type']) {
                                'motor' => '🛵',
                                'truk'  => '🚛',
                                default => '🚗',
                            };
                        @endphp
                        {{ $typeIcon }} {{ ucfirst($selectedSlot['vehicle_type']) }}
                        @if (! empty($selectedSlot['zone'])) · Zona {{ $selectedSlot['zone'] }} @endif
                        · Lantai {{ $selectedSlot['floor'] ?? '1' }}
                    </p>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-3">
                    @if ($activeTransaction)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500">Plat Nomor</span>
                            <span class="font-mono text-base font-extrabold tracking-wider text-gray-800">
                                {{ $activeTransaction['vehicle_plate'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500">Jam Masuk</span>
                            <span class="text-sm font-semibold text-gray-700">
                                {{ $activeTransaction['entry_time'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Durasi Berjalan</span>
                            <span class="rounded-full px-3 py-0.5 text-sm font-bold"
                                  style="background:#ffedd5;color:#c2410c">
                                {{ $activeTransaction['duration'] }}
                            </span>
                        </div>
                    @else
                        <p class="py-3 text-center text-sm text-gray-400">Tidak ada data transaksi aktif.</p>
                    @endif
                </div>

                {{-- Footer override --}}
                <div class="border-t border-gray-100 px-6 py-4" style="background:#f9fafb">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Manual Override</p>
                    <div class="flex gap-2">
                        <button type="button"
                            wire:click="overrideSlotStatus({{ $selectedSlot['id'] }}, 'available')"
                            class="flex-1 rounded-lg px-3 py-2 text-xs font-semibold transition-colors hover:bg-green-50"
                            style="border:1px solid #86efac;color:#15803d;background:#fff">
                            ✓ Kosongkan
                        </button>
                        <button type="button"
                            wire:click="overrideSlotStatus({{ $selectedSlot['id'] }}, 'reserved')"
                            class="flex-1 rounded-lg px-3 py-2 text-xs font-semibold transition-colors hover:bg-yellow-50"
                            style="border:1px solid #fde047;color:#854d0e;background:#fff">
                            ⏳ Reserved
                        </button>
                        <button type="button"
                            wire:click="overrideSlotStatus({{ $selectedSlot['id'] }}, 'disabled')"
                            class="flex-1 rounded-lg px-3 py-2 text-xs font-semibold transition-colors hover:bg-blue-50"
                            style="border:1px solid #93c5fd;color:#1d4ed8;background:#fff">
                            ✕ Disable
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
