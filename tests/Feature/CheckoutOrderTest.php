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
        $this->post(route('checkout.store'), $payload)->assertRedirect(route('checkout.create'));

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_missing_altered_and_foreign_session_tokens_are_rejected(): void
    {
        $course = $this->course();
        $validToken = (string) Str::uuid();

        $missingPayload = $this->customer($validToken);
        unset($missingPayload['checkout_token']);
        $this->withSession(['cart.course_ids' => [$course->id]])
            ->post(route('checkout.store'), $missingPayload)
            ->assertSessionHasErrors('checkout_token');

        $this->withSession($this->checkoutSession($course, $validToken))
            ->post(route('checkout.store'), $this->customer((string) Str::uuid()))
            ->assertRedirect(route('checkout.create'));

        $this->withSession(['cart.course_ids' => [$course->id], 'checkout.token' => (string) Str::uuid()])
            ->post(route('checkout.store'), $this->customer($validToken))
            ->assertRedirect(route('checkout.create'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_unpublished_or_deleted_course_before_post_blocks_order(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();
        $course->update(['is_published' => false]);

        $this->withSession($this->checkoutSession($course, $token))
            ->post(route('checkout.store'), $this->customer($token))
            ->assertRedirect(route('cart.index'));

        $course->update(['is_published' => true]);
        $deletedId = $course->id;
        $course->delete();
        $token = (string) Str::uuid();
        $this->withSession(['cart.course_ids' => [$deletedId], 'checkout.token' => $token])
            ->post(route('checkout.store'), $this->customer($token))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_price_changed_before_post_is_recalculated(): void
    {
        $course = $this->course(['price_anual' => '10.00']);
        $token = (string) Str::uuid();
        $course->update(['price_anual' => '20.00']);

        $this->withSession($this->checkoutSession($course, $token))
            ->post(route('checkout.store'), $this->customer($token));

        $this->assertSame('20.00', Order::firstOrFail()->total);
    }

    public function test_unicode_is_preserved_email_is_normalized_and_long_fields_are_rejected(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();
        $payload = $this->customer($token);
        $payload['first_name'] = '  Zoë María  ';
        $payload['last_name'] = "O’Connor-Núñez";
        $payload['email'] = '  ANA@EXAMPLE.COM ';

        $this->withSession($this->checkoutSession($course, $token))->post(route('checkout.store'), $payload);
        $order = Order::firstOrFail();
        $this->assertSame('Zoë María', $order->first_name);
        $this->assertSame("O’Connor-Núñez", $order->last_name);
        $this->assertSame('ana@example.com', $order->email);

        $token = (string) Str::uuid();
        $payload = $this->customer($token);
        $payload['first_name'] = str_repeat('a', 101);
        $payload['email'] = str_repeat('a', 250).'@x.com';
        $this->withSession($this->checkoutSession($course, $token))
            ->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors(['first_name', 'email']);
    }

    public function test_public_order_masks_customer_data_and_escapes_xss(): void
    {
        $course = $this->course();
        $token = (string) Str::uuid();
        $payload = $this->customer($token);
        $payload['first_name'] = '<script>alert(1)</script>';
        $payload['last_name'] = 'Núñez';

        $this->withSession($this->checkoutSession($course, $token))->post(route('checkout.store'), $payload);
        $order = Order::firstOrFail();

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee($order->email)
            ->assertDontSee($order->phone)
            ->assertDontSee((string) $order->document_number);
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
