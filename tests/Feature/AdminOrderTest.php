<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_normal_user_cannot_access_admin_orders(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.orders.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.orders.show', $this->order()))->assertForbidden();
    }

    public function test_admin_can_list_and_view_order_details(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->order();

        $this->actingAs($admin)->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Admin Test')
            ->assertSee($order->phone);
        $this->actingAs($admin)->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->email)
            ->assertSee('Nombres completos')
            ->assertSee('Admin Test')
            ->assertSee('Celular');
    }

    private function order(): Order
    {
        return Order::forceCreate([
            'order_number' => 'ORD-2026-TESTADMIN',
            'public_token' => Str::random(64),
            'checkout_token_hash' => hash('sha256', (string) Str::uuid()),
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin-order@example.com',
            'phone' => '999888777',
            'subtotal' => '10.00',
            'total' => '10.00',
            'currency' => 'PEN',
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }
}
