<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IzipayReviewReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_commercial_and_legal_pages_are_available(): void
    {
        foreach (['home', 'courses.index', 'contact', 'legal.terms', 'legal.privacy', 'legal.refunds', 'legal.delivery'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_catalog_hides_courses_without_a_valid_commercial_price(): void
    {
        $area = Area::create(['name' => 'Revisión', 'slug' => 'revision', 'sort_order' => 0, 'is_default' => true]);
        $available = $this->course($area, 'Curso disponible', 'disponible', '49.90');
        $unpriced = $this->course($area, 'Curso sin precio', 'sin-precio', null);

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee($available->title)
            ->assertDontSee($unpriced->title);

        $this->get(route('courses.show', $unpriced))->assertNotFound();
    }

    public function test_checkout_requires_legal_acceptance_and_has_no_card_fields(): void
    {
        $area = Area::create(['name' => 'Checkout', 'slug' => 'checkout-review', 'sort_order' => 0, 'is_default' => true]);
        $course = $this->course($area, 'Curso checkout', 'curso-checkout', '25.00');

        $response = $this->withSession(['cart.course_ids' => [$course->id]])->get(route('checkout.create'));

        $response->assertOk()
            ->assertSee('name="terms_accepted"', false)
            ->assertSee(route('legal.terms'), false)
            ->assertSee(route('legal.privacy'), false)
            ->assertDontSee('name="card_number"', false)
            ->assertDontSee('name="cvv"', false);
    }

    public function test_disabled_payment_state_is_professional_and_has_no_fake_button(): void
    {
        config()->set('services.izipay.payments_enabled', false);
        $order = Order::forceCreate([
            'order_number' => 'ORD-2026-'.strtoupper(Str::random(10)),
            'public_token' => Str::random(64),
            'checkout_token_hash' => hash('sha256', (string) Str::uuid()),
            'first_name' => 'Ana',
            'last_name' => 'Prueba',
            'email' => 'ana@example.com',
            'phone' => '999888777',
            'subtotal' => '10.00',
            'total' => '10.00',
            'currency' => 'PEN',
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_PENDING,
        ]);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Medio de pago temporalmente en proceso de habilitación')
            ->assertDontSee('Pagar ahora con Izipay')
            ->assertDontSee('credenciales sandbox');
    }

    private function course(Area $area, string $title, string $slug, ?string $price): Course
    {
        return Course::create([
            'area_id' => $area->id,
            'title' => $title,
            'slug' => $slug,
            'short_desc' => 'Curso digital de prueba comercial.',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => $price,
        ]);
    }
}
