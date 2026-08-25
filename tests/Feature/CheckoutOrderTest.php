<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class CheckoutOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_cart_cannot_open_checkout(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('cart.index'));
    }

    public function test_valid_guest_checkout_creates_pending_order_from_backend_values(): void
    {
        $course = $this->course(['price_anual' => '10.00']);
        $token = (string) Str::uuid();

        $response = $this->withSession($this->checkoutSession($course, $token))->post(route('checkout.store'), array_merge(
            $this->customer($token),
            ['price' => '0.01', 'total' => '0.01', 'currency' => 'USD'],
        ));

        $order = Order::with('items')->firstOrFail();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertNull($order->user_id);
        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('10.00', $order->total);
        $this->assertSame('PEN', $order->currency);
        $this->assertSame('10.00', $order->items->first()->unit_price);
        $this->assertSame('PEN', $order->items->first()->currency);
        $this->assertMatchesRegularExpression('/^ORD-\d{4}-[A-Z0-9]{10}$/', $order->order_number);
        $this->assertSame(64, strlen($order->public_token));
        $this->assertSame([], session('cart.course_ids', []));
    }

    public function test_invalid_customer_data_is_rejected_without_creating_order(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();

        $this->withSession($this->checkoutSession($course, $token))->post(route('checkout.store'), [
            'checkout_token' => $token,
            'first_name' => '',
            'email' => 'incorrecto',
        ])->assertSessionHasErrors(['first_name', 'last_name', 'email', 'phone']);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame([$course->id], session('cart.course_ids'));
    }

    public function test_checkout_form_does_not_submit_price_total_or_currency(): void
    {
        $course = $this->course();

        $response = $this->withSession(['cart.course_ids' => [$course->id]])
            ->get(route('checkout.create'));

        $response->assertOk();
        $response->assertDontSee('name="price"', false);
        $response->assertDontSee('name="total"', false);
        $response->assertDontSee('name="currency"', false);
    }

    public function test_order_snapshot_survives_a_course_price_change(): void
    {
        $course = $this->course(['price_anual' => '10.00']);
        $token = (string) Str::uuid();
        $this->withSession($this->checkoutSession($course, $token))
            ->post(route('checkout.store'), $this->customer($token));

        $course->update(['price_anual' => '20.00']);
        $order = Order::with('items')->firstOrFail();

        $this->assertSame('10.00', $order->items->first()->unit_price);
        $this->assertSame('10.00', $order->items->first()->line_total);
        $this->assertSame('10.00', $order->total);
    }

    public function test_same_checkout_token_does_not_create_duplicate_orders(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();
        $payload = $this->customer($token);

        $this->withSession($this->checkoutSession($course, $token))->post(route('checkout.store'), $payload);
        $this->post(route('checkout.store'), $payload)->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_cart_is_not_cleared_when_order_creation_fails(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();
        $mock = Mockery::mock(OrderService::class);
        $mock->shouldReceive('create')->once()->andThrow(new RuntimeException('fallo controlado'));
        $this->app->instance(OrderService::class, $mock);

        $this->withSession($this->checkoutSession($course, $token))
            ->post(route('checkout.store'), $this->customer($token))
            ->assertSessionHas('error');

        $this->assertSame([$course->id], session('cart.course_ids'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_page_uses_non_incremental_public_token(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();
        $this->withSession($this->checkoutSession($course, $token))
            ->post(route('checkout.store'), $this->customer($token));
        $order = Order::firstOrFail();

        $this->get('/orden/'.$order->id)->assertNotFound();
        $this->get(route('orders.show', $order))->assertOk()->assertSee($order->order_number);
    }

    private function customer(string $token): array
    {
        return [
            'checkout_token' => $token,
            'first_name' => 'Ana',
            'last_name' => 'Prueba',
            'email' => 'ana@example.com',
            'phone' => '+51 999 888 777',
            'document_type' => 'DNI',
            'document_number' => '12345678',
        ];
    }

    private function checkoutSession(Course $course, string $token): array
    {
        return ['cart.course_ids' => [$course->id], 'checkout.token' => $token];
    }

    private function course(array $attributes = []): Course
    {
        $area = Area::create(['name' => 'Área checkout', 'slug' => 'area-checkout', 'sort_order' => 0, 'is_default' => true]);

        return Course::create(array_merge([
            'area_id' => $area->id,
            'title' => 'Curso checkout',
            'slug' => 'curso-checkout',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => '10.00',
        ], $attributes));
    }
}
