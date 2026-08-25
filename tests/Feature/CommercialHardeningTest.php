<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class CommercialHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_is_atomic_when_an_item_fails(): void
    {
        $course = $this->course();
        $this->withSession(['cart.course_ids' => [$course->id]]);
        OrderItem::creating(fn () => throw new RuntimeException('fallo de ítem'));

        try {
            app(OrderService::class)->create($this->customer(), app(CartService::class), (string) Str::uuid(), null);
            $this->fail('La creación debía fallar.');
        } catch (RuntimeException $exception) {
            $this->assertSame('fallo de ítem', $exception->getMessage());
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_historical_order_survives_course_and_user_deletion(): void
    {
        $course = $this->course();
        $user = User::factory()->create();
        $this->withSession(['cart.course_ids' => [$course->id]]);
        $order = app(OrderService::class)->create($this->customer(), app(CartService::class), (string) Str::uuid(), $user->id);

        $course->delete();
        $user->delete();
        $order->refresh()->load('items');

        $this->assertNull($order->user_id);
        $this->assertNull($order->items->first()->course_id);
        $this->assertSame('Curso endurecimiento', $order->items->first()->course_title);
        $this->assertSame('10.00', $order->total);
    }

    public function test_transactional_models_reject_mass_assignment(): void
    {
        $this->expectException(MassAssignmentException::class);
        Order::create(['total' => '0.01', 'currency' => 'USD', 'status' => 'confirmed', 'payment_status' => 'paid']);
    }

    public function test_unique_identifiers_are_enforced_by_database(): void
    {
        $first = $this->order();

        $this->expectException(\Illuminate\Database\QueryException::class);
        Order::forceCreate(array_merge($first->getAttributes(), ['id' => null, 'created_at' => null, 'updated_at' => null]));
    }

    public function test_security_headers_and_http_methods_are_enforced(): void
    {
        $this->get(route('cart.index'))
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $this->get('/checkout/crear-orden')->assertNotFound();
        $this->post(route('cart.index'))->assertMethodNotAllowed();
    }

    public function test_commercial_mutation_forms_include_csrf_tokens(): void
    {
        $course = $this->course();

        $this->withSession(['cart.course_ids' => [$course->id]])
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('name="_token"', false);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_checkout_post_has_a_reasonable_rate_limit(): void
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->post(route('checkout.store'), [])->assertRedirect();
        }

        $this->post(route('checkout.store'), [])->assertTooManyRequests();
    }

    private function order(): Order
    {
        return Order::forceCreate([
            'order_number' => 'ORD-2026-UNIQUE0001',
            'public_token' => Str::random(64),
            'checkout_token_hash' => hash('sha256', (string) Str::uuid()),
            'first_name' => 'Ana',
            'last_name' => 'Prueba',
            'email' => 'ana@example.com',
            'phone' => '+51 999 888 777',
            'subtotal' => '10.00',
            'total' => '10.00',
            'currency' => 'PEN',
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_PENDING,
        ]);
    }

    private function customer(): array
    {
        return [
            'first_name' => 'Ana',
            'last_name' => 'Prueba',
            'email' => 'ana@example.com',
            'phone' => '+51 999 888 777',
            'document_type' => null,
            'document_number' => null,
        ];
    }

    private function course(): Course
    {
        $area = Area::create(['name' => 'Área endurecimiento', 'slug' => 'area-endurecimiento', 'sort_order' => 0, 'is_default' => true]);

        return Course::create([
            'area_id' => $area->id,
            'title' => 'Curso endurecimiento',
            'slug' => 'curso-endurecimiento',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => '10.00',
        ]);
    }
}
