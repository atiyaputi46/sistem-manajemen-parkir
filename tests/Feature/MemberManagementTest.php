<?php

use App\Livewire\MemberManagement;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('members management page can be rendered', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/members');

    $response->assertOk();
});

test('renew button is displayed only for expired members', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $expiredMember = Member::factory()->create([
        'status' => 'expired',
        'subscription_start' => now()->subMonths(2)->format('Y-m-d'),
        'subscription_end' => now()->subMonth()->format('Y-m-d'),
    ]);

    $activeMember = Member::factory()->create([
        'status' => 'active',
        'subscription_start' => now()->subDay()->format('Y-m-d'),
        'subscription_end' => now()->addDays(29)->format('Y-m-d'),
    ]);

    $pendingMember = Member::factory()->create([
        'status' => 'pending',
        'subscription_start' => now()->addDays(5)->format('Y-m-d'),
        'subscription_end' => now()->addDays(35)->format('Y-m-d'),
    ]);

    Livewire::test(MemberManagement::class)
        ->assertSeeHtml('wire:click="openRenewModal('.$expiredMember->id.')')
        ->assertDontSeeHtml('wire:click="openRenewModal('.$activeMember->id.')')
        ->assertDontSeeHtml('wire:click="openRenewModal('.$pendingMember->id.')');
});

test('renewMember updates member status and dates', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $expiredMember = Member::factory()->create([
        'status' => 'expired',
        'subscription_start' => now()->subMonths(2)->format('Y-m-d'),
        'subscription_end' => now()->subMonth()->format('Y-m-d'),
    ]);

    $expectedMessage = "Langganan {$expiredMember->full_name} berhasil diperpanjang hingga ".Carbon::today()->addDays(30)->format('d M Y').'.';

    Livewire::test(MemberManagement::class)
        ->call('openRenewModal', $expiredMember->id)
        ->assertSet('showRenewModal', true)
        ->assertSet('renewingMemberId', $expiredMember->id)
        ->assertSet('renewingMemberName', $expiredMember->full_name)
        ->assertSet('renewingEndDate', Carbon::today()->addDays(30)->format('d M Y'))
        ->call('renewMember', $expiredMember->id)
        ->assertSet('showRenewModal', false)
        ->assertSet('renewingMemberId', null)
        ->assertSee($expectedMessage);

    $expiredMember->refresh();

    expect($expiredMember->status)->toEqual('active');
    expect(Carbon::parse($expiredMember->subscription_start)->toDateString())->toEqual(Carbon::today()->toDateString());
    expect(Carbon::parse($expiredMember->subscription_end)->toDateString())->toEqual(Carbon::today()->addDays(30)->toDateString());
});
