<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_member_card_balance_and_recent_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir']);
        $member = Customer::query()->create([
            'name' => 'Member Admin Test',
            'phone' => '08123456789',
        ]);
        $card = $member->nfcCard()->create([
            'uid' => 'MEMBER-ADMIN-01',
            'balance' => 35000,
        ]);
        $card->transactions()->create([
            'cashier_id' => $cashier->id,
            'type' => 'top_up',
            'amount' => 35000,
            'payment_method' => 'tunai',
        ]);

        $this->actingAs($admin)->get(route('admin.members'))
            ->assertOk()
            ->assertSee('Member NFC')
            ->assertSee('Member Admin Test')
            ->assertSee('08123456789')
            ->assertSee('MEMBER-ADMIN-01')
            ->assertSee('35.000')
            ->assertSee('Isi saldo');
    }

    public function test_cashier_cannot_open_admin_member_page(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)->get(route('admin.members'))
            ->assertForbidden();
    }
}
