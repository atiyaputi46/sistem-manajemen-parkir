<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Member')]
class MemberManagement extends Component
{
    use WithPagination;

    // ===== Filter Tab =====
    public string $statusFilter = 'all';

    // ===== Modal Aktivasi =====
    public bool $showActivateModal = false;

    public ?int $activatingMemberId = null;

    public string $activatingMemberName = '';

    public string $activationEndDate = '';

    // ===== Modal Nonaktifkan =====
    public bool $showDeactivateModal = false;

    public ?int $deactivatingMemberId = null;

    public string $deactivatingMemberName = '';

    // ===== Modal Hapus =====
    public bool $showDeleteModal = false;

    public ?int $deletingMemberId = null;

    public string $deletingMemberName = '';

    // ===== Modal Riwayat Transaksi =====
    public bool $showHistoryModal = false;

    public ?int $selectedMemberId = null;

    public string $selectedMemberName = '';

    public string $selectedMemberPlate = '';

    // ===== Filter Tab =====

    public function setStatusFilter(string $filter): void
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }

    // ===== Aktivasi Member =====

    public function openActivateModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->activatingMemberId = $member->id;
        $this->activatingMemberName = $member->full_name;
        $this->activationEndDate = Carbon::today()->addDays(30)->format('d M Y');
        $this->showActivateModal = true;
    }

    public function closeActivateModal(): void
    {
        $this->showActivateModal = false;
        $this->activatingMemberId = null;
        $this->activatingMemberName = '';
        $this->activationEndDate = '';
    }

    public function activateMember(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $member->update([
            'status' => 'active',
            'subscription_start' => Carbon::today(),
            'subscription_end' => Carbon::today()->addDays(30),
        ]);

        $this->closeActivateModal();
        session()->flash('success', "Member {$member->full_name} berhasil diaktifkan. Langganan aktif hingga ".Carbon::today()->addDays(30)->format('d M Y').'.');
    }

    // ===== Nonaktifkan Member =====

    public function openDeactivateModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->deactivatingMemberId = $member->id;
        $this->deactivatingMemberName = $member->full_name;
        $this->showDeactivateModal = true;
    }

    public function closeDeactivateModal(): void
    {
        $this->showDeactivateModal = false;
        $this->deactivatingMemberId = null;
        $this->deactivatingMemberName = '';
    }

    public function deactivateMember(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $member->update(['status' => 'expired']);

        $this->closeDeactivateModal();
        session()->flash('success', "Member {$member->full_name} berhasil dinonaktifkan.");
    }

    // ===== Hapus Member =====

    public function openDeleteModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->deletingMemberId = $member->id;
        $this->deletingMemberName = $member->full_name;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingMemberId = null;
        $this->deletingMemberName = '';
    }

    public function deleteMember(int $memberId): void
    {
        Member::findOrFail($memberId)->delete();

        $this->closeDeleteModal();
        session()->flash('success', 'Data pendaftaran berhasil dihapus.');
    }

    // ===== Riwayat Transaksi =====

    public function openHistoryModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->selectedMemberId = $member->id;
        $this->selectedMemberName = $member->full_name;
        $this->selectedMemberPlate = $member->vehicle_plate;
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->selectedMemberId = null;
        $this->selectedMemberName = '';
        $this->selectedMemberPlate = '';
    }

    public function render(): View
    {
        $membersQuery = Member::query()->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $membersQuery->where('status', $this->statusFilter);
        }

        $members = $membersQuery->paginate(15);

        $transactions = [];
        if ($this->showHistoryModal && $this->selectedMemberPlate !== '') {
            $transactions = ParkingTransaction::where('vehicle_plate', $this->selectedMemberPlate)
                ->orderBy('entry_time', 'desc')
                ->limit(20)
                ->get();
        }

        $counts = [
            'all' => Member::count(),
            'pending' => Member::where('status', 'pending')->count(),
            'active' => Member::where('status', 'active')->count(),
            'expired' => Member::where('status', 'expired')->count(),
        ];

        return view('livewire.member-management', [
            'members' => $members,
            'transactions' => $transactions,
            'counts' => $counts,
        ]);
    }
}
