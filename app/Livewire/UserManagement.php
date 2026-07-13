<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Komponen Livewire untuk Manajemen Karyawan.
 * Menangani penambahan karyawan baru (admin/staff), pengeditan data profil/password karyawan,
 * dan penghapusan akun karyawan (petugas) dengan proteksi agar admin tidak menghapus dirinya sendiri.
 */
#[Title('Manajemen Karyawan')]
class UserManagement extends Component
{
    // ===== Modal Tambah =====

    /**
     * Flag status penampilan modal tambah karyawan baru.
     */
    public bool $showAddModal = false;

    /**
     * Menyimpan nilai input nama untuk karyawan baru.
     */
    public string $addName = '';

    /**
     * Menyimpan nilai input email untuk karyawan baru.
     */
    public string $addEmail = '';

    /**
     * Menyimpan nilai input password untuk karyawan baru.
     */
    public string $addPassword = '';

    /**
     * Menyimpan nilai input konfirmasi password untuk karyawan baru.
     */
    public string $addPasswordConfirmation = '';

    /**
     * Menyimpan nilai pilihan role untuk karyawan baru ('admin' atau 'staff').
     */
    public string $addRole = 'staff';

    // ===== Modal Edit =====

    /**
     * Flag status penampilan modal edit data karyawan.
     */
    public bool $showEditModal = false;

    /**
     * ID karyawan yang sedang diedit.
     */
    public ?int $editingUserId = null;

    /**
     * Menyimpan nilai input nama untuk edit karyawan.
     */
    public string $editName = '';

    /**
     * Menyimpan nilai input email untuk edit karyawan.
     */
    public string $editEmail = '';

    /**
     * Menyimpan nilai input password baru jika ingin diganti.
     */
    public string $editPassword = '';

    /**
     * Menyimpan nilai input konfirmasi password baru.
     */
    public string $editPasswordConfirmation = '';

    /**
     * Menyimpan nilai pilihan role untuk edit karyawan.
     */
    public string $editRole = 'staff';

    // ===== Modal Hapus =====

    /**
     * Flag status penampilan modal konfirmasi hapus karyawan.
     */
    public bool $showDeleteModal = false;

    /**
     * ID karyawan yang akan dihapus.
     */
    public ?int $deletingUserId = null;

    /**
     * Nama karyawan yang akan dihapus.
     */
    public string $deletingUserName = '';

    // ===== Tambah Karyawan =====

    /**
     * Membuka modal tambah karyawan dan mereset isi formulir agar bersih.
     */
    public function openAddModal(): void
    {
        $this->resetAddForm();
        $this->showAddModal = true;
    }

    /**
     * Menutup modal tambah karyawan dan mereset isi formulir.
     */
    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    /**
     * Memvalidasi form input pendaftaran karyawan baru, menyimpan datanya ke database,
     * menyandi password menggunakan Hash, dan menutup modal.
     */
    public function saveUser(): void
    {
        // Validasi input pendaftaran karyawan baru
        $this->validate([
            'addName' => ['required', 'string', 'max:100'],
            'addEmail' => ['required', 'email', 'max:100', 'unique:users,email'],
            'addPassword' => ['required', 'string', 'min:8', 'same:addPasswordConfirmation'],
            'addPasswordConfirmation' => ['required'],
            'addRole' => ['required', 'in:admin,staff'],
        ], [
            'addName.required' => 'Nama wajib diisi.',
            'addEmail.required' => 'Email wajib diisi.',
            'addEmail.email' => 'Format email tidak valid.',
            'addEmail.unique' => 'Email sudah digunakan.',
            'addPassword.required' => 'Password wajib diisi.',
            'addPassword.min' => 'Password minimal 8 karakter.',
            'addPassword.same' => 'Konfirmasi password tidak cocok.',
            'addRole.required' => 'Role wajib dipilih.',
        ]);

        // Buat record baru di tabel users
        User::create([
            'name' => $this->addName,
            'email' => $this->addEmail,
            'password' => Hash::make($this->addPassword),
            'role' => $this->addRole,
        ]);

        $this->closeAddModal();
        session()->flash('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Mengatur ulang nilai properti formulir tambah karyawan ke kondisi awal.
     */
    private function resetAddForm(): void
    {
        $this->addName = '';
        $this->addEmail = '';
        $this->addPassword = '';
        $this->addPasswordConfirmation = '';
        $this->addRole = 'staff';
        $this->resetErrorBag();
    }

    // ===== Edit Karyawan =====

    /**
     * Membuka modal edit karyawan dengan memuat data karyawan terpilih ke dalam properti form.
     *
     * @param  int  $userId  ID karyawan yang akan diedit
     */
    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPassword = '';
        $this->editPasswordConfirmation = '';
        $this->editRole = $user->role;
        $this->resetErrorBag();

        $this->showEditModal = true;
    }

    /**
     * Menutup modal edit karyawan dan membersihkan pesan kesalahan validasi.
     */
    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingUserId = null;
        $this->resetErrorBag();
    }

    /**
     * Memvalidasi form input pembaruan data karyawan (dengan pengabaian keunikan email untuk user itu sendiri),
     * serta memperbarui data di database (termasuk mengganti password jika diisi).
     */
    public function updateUser(): void
    {
        $rules = [
            'editName' => ['required', 'string', 'max:100'],
            'editEmail' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'editRole' => ['required', 'in:admin,staff'],
        ];

        $messages = [
            'editName.required' => 'Nama wajib diisi.',
            'editEmail.required' => 'Email wajib diisi.',
            'editEmail.email' => 'Format email tidak valid.',
            'editEmail.unique' => 'Email sudah digunakan.',
            'editRole.required' => 'Role wajib dipilih.',
        ];

        // Jika input password baru diisi, tambahkan aturan validasi password
        if ($this->editPassword !== '') {
            $rules['editPassword'] = ['string', 'min:8', 'same:editPasswordConfirmation'];
            $rules['editPasswordConfirmation'] = ['required'];
            $messages['editPassword.min'] = 'Password minimal 8 karakter.';
            $messages['editPassword.same'] = 'Konfirmasi password tidak cocok.';
        }

        $this->validate($rules, $messages);

        $user = User::findOrFail($this->editingUserId);

        $data = [
            'name' => $this->editName,
            'email' => $this->editEmail,
            'role' => $this->editRole,
        ];

        // Enkripsi password baru jika diinput
        if ($this->editPassword !== '') {
            $data['password'] = Hash::make($this->editPassword);
        }

        $user->update($data);

        $this->closeEditModal();
        session()->flash('success', 'Data karyawan berhasil diperbarui.');
    }

    // ===== Hapus Karyawan =====

    /**
     * Membuka modal konfirmasi hapus karyawan.
     * Mencegah admin memicu modal hapus untuk akun yang sedang digunakannya saat ini.
     *
     * @param  int  $userId  ID karyawan yang akan dihapus
     */
    public function openDeleteModal(int $userId): void
    {
        if ($userId === Auth::id()) {
            return;
        }

        $user = User::findOrFail($userId);
        $this->deletingUserId = $user->id;
        $this->deletingUserName = $user->name;
        $this->showDeleteModal = true;
    }

    /**
     * Menutup modal konfirmasi hapus karyawan.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
    }

    /**
     * Menghapus record akun karyawan secara permanen dari database.
     * Mencegah admin menghapus akunnya sendiri.
     */
    public function deleteUser(): void
    {
        if ($this->deletingUserId === Auth::id()) {
            return;
        }

        User::findOrFail($this->deletingUserId)->delete();
        $this->closeDeleteModal();
        session()->flash('success', 'Karyawan berhasil dihapus.');
    }

    /**
     * Merender file view Blade user-management dengan mengirimkan daftar seluruh pengguna sistem.
     */
    public function render(): View
    {
        return view('livewire.user-management', [
            'users' => User::orderBy('created_at', 'desc')->get(),
        ]);
    }
}
