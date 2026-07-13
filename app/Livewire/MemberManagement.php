<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Komponen Livewire untuk Manajemen Member.
 * Menangani tampilan data pendaftaran member, filter tab status,
 * proses aktivasi member baru, nonaktivasi member lama, penghapusan, dan riwayat transaksi member.
 */
#[Title('Manajemen Member')]
class MemberManagement extends Component
{
    use WithPagination;

    // ===== Filter Tab =====

    /**
     * Filter tab status member yang dipilih: 'all', 'pending', 'active', atau 'expired'.
     */
    public string $statusFilter = 'all';

    // ===== Modal Aktivasi =====

    /**
     * Flag status penampilan modal persetujuan/aktivasi member.
     */
    public bool $showActivateModal = false;

    /**
     * ID member yang sedang diajukan untuk diaktifkan.
     */
    public ?int $activatingMemberId = null;

    /**
     * Nama member yang sedang diajukan untuk diaktifkan.
     */
    public string $activatingMemberName = '';

    /**
     * Tanggal akhir masa berlaku keanggotaan member (default 30 hari ke depan).
     */
    public string $activationEndDate = '';

    // ===== Modal Nonaktifkan =====

    /**
     * Flag status penampilan modal konfirmasi penonaktifan member.
     */
    public bool $showDeactivateModal = false;

    /**
     * ID member yang sedang ditargetkan untuk dinonaktifkan.
     */
    public ?int $deactivatingMemberId = null;

    /**
     * Nama member yang sedang ditargetkan untuk dinonaktifkan.
     */
    public string $deactivatingMemberName = '';

    // ===== Modal Hapus =====

    /**
     * Flag status penampilan modal konfirmasi penghapusan data pendaftaran member.
     */
    public bool $showDeleteModal = false;

    /**
     * ID member yang sedang ditargetkan untuk dihapus.
     */
    public ?int $deletingMemberId = null;

    /**
     * Nama member yang sedang ditargetkan untuk dihapus.
     */
    public string $deletingMemberName = '';

    // ===== Modal Perpanjang =====

    /**
     * Flag status penampilan modal konfirmasi perpanjangan member.
     */
    public bool $showRenewModal = false;

    /**
     * ID member yang sedang ditargetkan untuk diperpanjang.
     */
    public ?int $renewingMemberId = null;

    /**
     * Nama member yang sedang ditargetkan untuk diperpanjang.
     */
    public string $renewingMemberName = '';

    /**
     * Tanggal akhir masa berlaku keanggotaan setelah diperpanjang.
     */
    public string $renewingEndDate = '';

    // ===== Modal Riwayat Transaksi =====

    /**
     * Flag status penampilan modal daftar riwayat parkir member terkait.
     */
    public bool $showHistoryModal = false;

    /**
     * ID member yang riwayat transaksinya sedang dilihat.
     */
    public ?int $selectedMemberId = null;

    /**
     * Nama member yang riwayat transaksinya sedang dilihat.
     */
    public string $selectedMemberName = '';

    /**
     * Nomor plat kendaraan member yang riwayat transaksinya sedang dilihat.
     */
    public string $selectedMemberPlate = '';

    // ===== Filter Tab =====

    /**
     * Mengubah filter tab status member yang aktif dan mereset pagination ke halaman 1.
     *
     * @param  string  $filter  Status filter ('all', 'pending', 'active', 'expired')
     */
    public function setStatusFilter(string $filter): void
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }

    // ===== Aktivasi Member =====

    /**
     * Membuka modal aktivasi member pendatang baru dengan menyiapkan kalkulasi tanggal kedaluwarsa 30 hari ke depan.
     *
     * @param  int  $memberId  ID member yang akan diaktifkan
     */
    public function openActivateModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->activatingMemberId = $member->id;
        $this->activatingMemberName = $member->full_name;
        $this->activationEndDate = Carbon::today()->addDays(30)->format('d M Y');
        $this->showActivateModal = true;
    }

    /**
     * Menutup modal aktivasi member dan membersihkan state variabel.
     */
    public function closeActivateModal(): void
    {
        $this->showActivateModal = false;
        $this->activatingMemberId = null;
        $this->activatingMemberName = '';
        $this->activationEndDate = '';
    }

    /**
     * Mengaktifkan member secara definitif di database.
     * Menyetel status menjadi 'active', subscription_start hari ini, dan subscription_end hari ini + 30 hari.
     *
     * @param  int  $memberId  ID member yang akan diaktifkan
     */
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

    /**
     * Membuka modal konfirmasi untuk menonaktifkan status member aktif.
     *
     * @param  int  $memberId  ID member yang akan dinonaktifkan
     */
    public function openDeactivateModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->deactivatingMemberId = $member->id;
        $this->deactivatingMemberName = $member->full_name;
        $this->showDeactivateModal = true;
    }

    /**
     * Menutup modal nonaktifkan member dan membersihkan state variabel.
     */
    public function closeDeactivateModal(): void
    {
        $this->showDeactivateModal = false;
        $this->deactivatingMemberId = null;
        $this->deactivatingMemberName = '';
    }

    /**
     * Mengubah status member terpilih di database menjadi 'expired'.
     *
     * @param  int  $memberId  ID member yang akan dinonaktifkan
     */
    public function deactivateMember(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $member->update(['status' => 'expired']);

        $this->closeDeactivateModal();
        session()->flash('success', "Member {$member->full_name} berhasil dinonaktifkan.");
    }

    // ===== Hapus Member =====

    /**
     * Membuka modal konfirmasi untuk menghapus data pendaftaran member.
     *
     * @param  int  $memberId  ID member yang akan dihapus
     */
    public function openDeleteModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->deletingMemberId = $member->id;
        $this->deletingMemberName = $member->full_name;
        $this->showDeleteModal = true;
    }

    /**
     * Menutup modal hapus member dan membersihkan state variabel.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingMemberId = null;
        $this->deletingMemberName = '';
    }

    /**
     * Menghapus secara permanen record data member dari database.
     *
     * @param  int  $memberId  ID member yang akan dihapus
     */
    public function deleteMember(int $memberId): void
    {
        Member::findOrFail($memberId)->delete();

        $this->closeDeleteModal();
        session()->flash('success', 'Data pendaftaran berhasil dihapus.');
    }

    // ===== Perpanjang Member =====

    /**
     * Membuka modal konfirmasi perpanjangan member dengan menyiapkan tanggal kedaluwarsa 30 hari ke depan.
     *
     * @param  int  $memberId  ID member yang akan diperpanjang
     */
    public function openRenewModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->renewingMemberId = $member->id;
        $this->renewingMemberName = $member->full_name;
        $this->renewingEndDate = Carbon::today()->addDays(30)->format('d M Y');
        $this->showRenewModal = true;
    }

    /**
     * Menutup modal perpanjang member dan membersihkan state variabel.
     */
    public function closeRenewModal(): void
    {
        $this->showRenewModal = false;
        $this->renewingMemberId = null;
        $this->renewingMemberName = '';
        $this->renewingEndDate = '';
    }

    /**
     * Memperpanjang keanggotaan member secara definitif di database.
     * Menyetel status menjadi 'active', subscription_start hari ini, dan subscription_end hari ini + 30 hari.
     *
     * @param  int  $memberId  ID member yang akan diperpanjang
     */
    public function renewMember(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $member->update([
            'status' => 'active',
            'subscription_start' => Carbon::today(),
            'subscription_end' => Carbon::today()->addDays(30),
        ]);

        $this->closeRenewModal();
        session()->flash('success', "Langganan {$member->full_name} berhasil diperpanjang hingga ".Carbon::today()->addDays(30)->format('d M Y').'.');
    }

    // ===== Riwayat Transaksi =====

    /**
     * Membuka modal untuk melihat log riwayat transaksi parkir yang dikaitkan dengan plat nomor member terkait.
     *
     * @param  int  $memberId  ID member yang akan dilihat riwayatnya
     */
    public function openHistoryModal(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        $this->selectedMemberId = $member->id;
        $this->selectedMemberName = $member->full_name;
        $this->selectedMemberPlate = $member->vehicle_plate;
        $this->showHistoryModal = true;
    }

    /**
     * Menutup modal riwayat transaksi member dan mereset nilai plat nomor terpilih.
     */
    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->selectedMemberId = null;
        $this->selectedMemberName = '';
        $this->selectedMemberPlate = '';
    }

    /**
     * Merender file view Blade member-management dengan daftar data member terpaginasi,
     * log transaksi (jika melihat riwayat), dan jumlah total kalkulasi berdasarkan status.
     */
    public function render(): View
    {
        $membersQuery = Member::query()->orderBy('created_at', 'desc');

        // Terapkan filter tab status jika bukan 'all'
        if ($this->statusFilter !== 'all') {
            $membersQuery->where('status', $this->statusFilter);
        }

        $members = $membersQuery->paginate(15);

        // Jika modal riwayat sedang aktif, ambil 20 transaksi parkir terakhir dari plat nomor member
        $transactions = [];
        if ($this->showHistoryModal && $this->selectedMemberPlate !== '') {
            $transactions = ParkingTransaction::where('vehicle_plate', $this->selectedMemberPlate)
                ->orderBy('entry_time', 'desc')
                ->limit(20)
                ->get();
        }

        // Kumpulkan jumlah total data member per status keanggotaan
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
