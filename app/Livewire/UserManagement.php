<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manajemen Karyawan')]
class UserManagement extends Component
{
    // ===== Modal Tambah =====
    public bool $showAddModal = false;

    public string $addName = '';

    public string $addEmail = '';

    public string $addPassword = '';

    public string $addPasswordConfirmation = '';

    public string $addRole = 'staff';

    // ===== Modal Edit =====
    public bool $showEditModal = false;

    public ?int $editingUserId = null;

    public string $editName = '';

    public string $editEmail = '';

    public string $editPassword = '';

    public string $editPasswordConfirmation = '';

    public string $editRole = 'staff';

    // ===== Modal Hapus =====
    public bool $showDeleteModal = false;

    public ?int $deletingUserId = null;

    public string $deletingUserName = '';

    // ===== Tambah Karyawan =====

    public function openAddModal(): void
    {
        $this->resetAddForm();
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function saveUser(): void
    {
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

        User::create([
            'name' => $this->addName,
            'email' => $this->addEmail,
            'password' => Hash::make($this->addPassword),
            'role' => $this->addRole,
        ]);

        $this->closeAddModal();
        session()->flash('success', 'Karyawan berhasil ditambahkan.');
    }

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

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingUserId = null;
        $this->resetErrorBag();
    }

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

        if ($this->editPassword !== '') {
            $data['password'] = Hash::make($this->editPassword);
        }

        $user->update($data);

        $this->closeEditModal();
        session()->flash('success', 'Data karyawan berhasil diperbarui.');
    }

    // ===== Hapus Karyawan =====

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

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
    }

    public function deleteUser(): void
    {
        if ($this->deletingUserId === Auth::id()) {
            return;
        }

        User::findOrFail($this->deletingUserId)->delete();
        $this->closeDeleteModal();
        session()->flash('success', 'Karyawan berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.user-management', [
            'users' => User::orderBy('created_at', 'desc')->get(),
        ]);
    }
}
