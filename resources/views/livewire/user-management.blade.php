<div>

    {{-- ===== HEADER ===== --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Karyawan</h2>
            <p class="text-sm text-gray-500">Kelola akun petugas dan admin sistem parkir</p>
        </div>
        <button wire:click="openAddModal"
            class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            + Tambah Karyawan
        </button>
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

    {{-- ===== TABEL USER ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Role</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat Pada</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm font-medium text-gray-800">
                            {{ $user->name }}
                            @if ($user->id === auth()->id())
                                <span class="ml-1.5 inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600">Kamu</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            @if ($user->role === 'admin')
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">Admin</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Staff</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-500">{{ $user->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $user->id }})"
                                    class="rounded-md px-3 py-1.5 text-xs font-medium text-indigo-600 ring-1 ring-indigo-200 hover:bg-indigo-50 transition-colors">
                                    Edit
                                </button>

                                @if ($user->id === auth()->id())
                                    <div class="group relative inline-block">
                                        <button disabled
                                            class="cursor-not-allowed rounded-md px-3 py-1.5 text-xs font-medium text-gray-400 ring-1 ring-gray-200 bg-gray-50">
                                            Hapus
                                        </button>
                                        <div class="pointer-events-none absolute bottom-full right-0 mb-2 hidden w-max rounded-md bg-gray-800 px-2.5 py-1.5 text-xs text-white shadow-lg group-hover:block">
                                            Tidak dapat menghapus akun sendiri
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="openDeleteModal({{ $user->id }})"
                                        class="rounded-md px-3 py-1.5 text-xs font-medium text-red-600 ring-1 ring-red-200 hover:bg-red-50 transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">Belum ada data karyawan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===== MODAL TAMBAH KARYAWAN ===== --}}
    @if ($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-800">Tambah Karyawan</h3>
                    <button wire:click="closeAddModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input wire:model="addName" type="text" placeholder="Nama lengkap"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('addName') border-red-400 @enderror" />
                        @error('addName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                        <input wire:model="addEmail" type="email" placeholder="email@contoh.com"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('addEmail') border-red-400 @enderror" />
                        @error('addEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Password <span class="text-red-500">*</span></label>
                        <input wire:model="addPassword" type="password" placeholder="Minimal 8 karakter"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('addPassword') border-red-400 @enderror" />
                        @error('addPassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input wire:model="addPasswordConfirmation" type="password" placeholder="Ulangi password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>
                    {{-- Role --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Role <span class="text-red-500">*</span></label>
                        <select wire:model="addRole"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('addRole') border-red-400 @enderror">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('addRole') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeAddModal"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveUser" wire:loading.attr="disabled"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveUser">Simpan</span>
                        <span wire:loading wire:target="saveUser">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL EDIT KARYAWAN ===== --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-800">Edit Karyawan</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input wire:model="editName" type="text"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editName') border-red-400 @enderror" />
                        @error('editName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                        <input wire:model="editEmail" type="email"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editEmail') border-red-400 @enderror" />
                        @error('editEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Role --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Role <span class="text-red-500">*</span></label>
                        <select wire:model="editRole"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editRole') border-red-400 @enderror">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('editRole') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Password baru (opsional) --}}
                    <div class="rounded-lg bg-gray-50 p-3 space-y-3">
                        <p class="text-xs font-medium text-gray-500">Ubah Password <span class="font-normal">(opsional — kosongkan jika tidak diubah)</span></p>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Password Baru</label>
                            <input wire:model="editPassword" type="password" placeholder="Minimal 8 karakter"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none @error('editPassword') border-red-400 @enderror" />
                            @error('editPassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi Password Baru</label>
                            <input wire:model="editPasswordConfirmation" type="password" placeholder="Ulangi password baru"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeEditModal"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="updateUser" wire:loading.attr="disabled"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="updateUser">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateUser">Menyimpan...</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800">Hapus Karyawan</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Yakin ingin menghapus akun <span class="font-semibold text-gray-700">{{ $deletingUserName }}</span>?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex gap-3 border-t border-gray-100 px-6 py-4">
                    <button wire:click="closeDeleteModal"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteUser" wire:loading.attr="disabled"
                        class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="deleteUser">Ya, Hapus</span>
                        <span wire:loading wire:target="deleteUser">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
