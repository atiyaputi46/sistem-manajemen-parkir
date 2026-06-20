<div>

    {{-- ===== HEADER ===== --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Member</h2>
            <p class="text-sm text-gray-500">Kelola pendaftaran dan langganan member parkir</p>
        </div>
    </div>

    {{-- ===== FLASH MESSAGE ===== --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700">
            <svg class="h-4 w-4 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== TAB FILTER ===== --}}
    <div class="mb-4 flex items-center gap-2 border-b border-gray-200">
        @php
            $tabs = [
                ['key' => 'all',     'label' => 'Semua',     'color' => 'gray'],
                ['key' => 'pending', 'label' => 'Pending',   'color' => 'yellow'],
                ['key' => 'active',  'label' => 'Aktif',     'color' => 'green'],
                ['key' => 'expired', 'label' => 'Kadaluarsa','color' => 'red'],
            ];
        @endphp

        @foreach ($tabs as $tab)
            <button
                wire:click="setStatusFilter('{{ $tab['key'] }}')"
                @class([
                    'relative px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none',
                    '-mb-px border-b-2 border-indigo-600 text-indigo-600' => $statusFilter === $tab['key'],
                    'border-b-2 border-transparent text-gray-500 hover:text-gray-700' => $statusFilter !== $tab['key'],
                ])
            >
                {{ $tab['label'] }}
                <span @class([
                    'ml-1.5 inline-flex items-center rounded-full px-1.5 py-0.5 text-xs font-semibold',
                    'bg-gray-100 text-gray-600' => $tab['key'] === 'all',
                    'bg-yellow-100 text-yellow-700' => $tab['key'] === 'pending',
                    'bg-green-100 text-green-700' => $tab['key'] === 'active',
                    'bg-red-100 text-red-700' => $tab['key'] === 'expired',
                ])>{{ $counts[$tab['key']] }}</span>
            </button>
        @endforeach
    </div>

    {{-- ===== TABEL MEMBER ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Plat Nomor</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kendaraan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">No. HP</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Daftar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Langganan Hingga</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($members as $member)
                    @php
                        $isExpiredByDate = $member->status === 'active'
                            && $member->subscription_end !== null
                            && \Carbon\Carbon::parse($member->subscription_end)->lt(\Carbon\Carbon::today());
                    @endphp
                    <tr wire:key="member-{{ $member->id }}" class="hover:bg-gray-50 transition-colors">

                        {{-- Nama --}}
                        <td class="px-5 py-3.5 text-sm font-medium text-gray-800">
                            {{ $member->full_name }}
                        </td>

                        {{-- Plat Nomor --}}
                        <td class="px-5 py-3.5 text-sm font-mono font-semibold text-gray-700">
                            {{ $member->vehicle_plate }}
                        </td>

                        {{-- Jenis Kendaraan --}}
                        <td class="px-5 py-3.5 text-sm text-gray-600 capitalize">
                            {{ $member->vehicle_type }}
                        </td>

                        {{-- No. HP --}}
                        <td class="px-5 py-3.5 text-sm text-gray-600">
                            {{ $member->phone ?? '-' }}
                        </td>

                        {{-- Status + badge kadaluarsa --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if ($member->status === 'pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-800">Pending</span>
                                @elseif ($member->status === 'active')
                                    <span class="inline-flex items-center rounded-full bg-green-500 px-2.5 py-0.5 text-xs font-semibold text-white">Aktif</span>
                                @elseif ($member->status === 'expired')
                                    <span class="inline-flex items-center rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-semibold text-white">Kadaluarsa</span>
                                @endif

                                @if ($isExpiredByDate)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">KADALUARSA</span>
                                @endif
                            </div>
                        </td>

                        {{-- Tanggal Daftar --}}
                        <td class="px-5 py-3.5 text-sm text-gray-500">
                            {{ $member->created_at?->format('d M Y') ?? '-' }}
                        </td>

                        {{-- Langganan Hingga --}}
                        <td class="px-5 py-3.5 text-sm text-gray-500">
                            @if ($member->subscription_end)
                                <span @class(['text-red-600 font-medium' => $isExpiredByDate])>
                                    {{ \Carbon\Carbon::parse($member->subscription_end)->format('d M Y') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2 flex-wrap">

                                {{-- Riwayat (semua status) --}}
                                <button wire:click="openHistoryModal({{ $member->id }})"
                                    class="rounded-md px-3 py-1.5 text-xs font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                                    Riwayat
                                </button>

                                {{-- Aktivasi (hanya pending) --}}
                                @if ($member->status === 'pending')
                                    <button wire:click="openActivateModal({{ $member->id }})"
                                        class="rounded-md px-3 py-1.5 text-xs font-medium text-green-700 ring-1 ring-green-300 hover:bg-green-50 transition-colors">
                                        Aktivasi
                                    </button>
                                @endif

                                {{-- Nonaktifkan (hanya active) --}}
                                @if ($member->status === 'active')
                                    <button wire:click="openDeactivateModal({{ $member->id }})"
                                        class="rounded-md px-3 py-1.5 text-xs font-medium text-orange-700 ring-1 ring-orange-300 hover:bg-orange-50 transition-colors">
                                        Nonaktifkan
                                    </button>
                                @endif

                                {{-- Hapus (hanya pending) --}}
                                @if ($member->status === 'pending')
                                    <button wire:click="openDeleteModal({{ $member->id }})"
                                        class="rounded-md px-3 py-1.5 text-xs font-medium text-red-600 ring-1 ring-red-200 hover:bg-red-50 transition-colors">
                                        Hapus
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                            @if ($statusFilter === 'all')
                                Belum ada data member.
                            @else
                                Tidak ada member dengan status <span class="font-medium">{{ $statusFilter }}</span>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($members->hasPages())
            <div class="border-t border-gray-100 px-5 py-3">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL KONFIRMASI AKTIVASI ===== --}}
    @if ($showActivateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">Aktivasi Member</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Aktifkan member <span class="font-semibold text-gray-700">{{ $activatingMemberName }}</span>?
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Langganan aktif hingga: <span class="font-semibold text-green-700">{{ $activationEndDate }}</span>
                    </p>
                </div>
                <div class="flex gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeActivateModal"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="activateMember({{ $activatingMemberId }})" wire:loading.attr="disabled"
                        class="flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="activateMember">Ya, Aktifkan</span>
                        <span wire:loading wire:target="activateMember">Mengaktifkan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL KONFIRMASI NONAKTIFKAN ===== --}}
    @if ($showDeactivateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">Nonaktifkan Member</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Nonaktifkan member <span class="font-semibold text-gray-700">{{ $deactivatingMemberName }}</span>?
                        Langganan akan dihentikan segera.
                    </p>
                </div>
                <div class="flex gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeDeactivateModal"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deactivateMember({{ $deactivatingMemberId }})" wire:loading.attr="disabled"
                        class="flex-1 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="deactivateMember">Ya, Nonaktifkan</span>
                        <span wire:loading wire:target="deactivateMember">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">Hapus Pendaftaran</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Hapus data pendaftaran <span class="font-semibold text-gray-700">{{ $deletingMemberName }}</span>?
                        Tindakan ini tidak bisa dibatalkan.
                    </p>
                </div>
                <div class="flex gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeDeleteModal"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteMember({{ $deletingMemberId }})" wire:loading.attr="disabled"
                        class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="deleteMember">Ya, Hapus</span>
                        <span wire:loading wire:target="deleteMember">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL RIWAYAT TRANSAKSI ===== --}}
    @if ($showHistoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-3xl rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Riwayat Transaksi</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $selectedMemberName }} &mdash; <span class="font-mono font-semibold">{{ $selectedMemberPlate }}</span>
                        </p>
                    </div>
                    <button wire:click="closeHistoryModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-auto max-h-[60vh]">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jam Keluar</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Durasi</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Biaya</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($transactions as $trx)
                                @php
                                    $durationMinutes = $trx->duration_minutes;
                                    $durationLabel = '-';
                                    if ($durationMinutes !== null) {
                                        $hours = intdiv($durationMinutes, 60);
                                        $mins  = $durationMinutes % 60;
                                        $durationLabel = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
                                    }
                                @endphp
                                <tr wire:key="trx-{{ $trx->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($trx->entry_time)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">
                                        {{ $trx->exit_time ? \Carbon\Carbon::parse($trx->exit_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">
                                        {{ $durationLabel }}
                                    </td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800">
                                        Rp {{ number_format($trx->fee, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($trx->status === 'parked')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Parkir</span>
                                        @elseif ($trx->status === 'exited')
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">Keluar</span>
                                        @elseif ($trx->status === 'flagged')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">Flagged</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">
                                        Belum ada riwayat transaksi untuk plat nomor ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeHistoryModal"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
