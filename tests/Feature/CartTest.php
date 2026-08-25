<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_is_initially_empty(): void
    {
        $this->get(route('cart.index'))->assertOk()->assertSee('Tu carrito está vacío');
    }

    public function test_it_adds_a_published_priced_course_and_uses_backend_values(): void
    {
        $course = $this->course();

        $this->post(route('cart.store', $course), [
            'price' => '0.01',
            'currency' => 'USD',
        ])->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))
            ->assertSee($course->title)
            ->assertSee('S/ 10.00')
            ->assertSee('PEN');
    }

    public function test_it_rejects_drafts_missing_courses_and_courses_without_price(): void
    {
        $draft = $this->course(['slug' => 'borrador', 'is_published' => false]);
        $withoutPrice = $this->course(['slug' => 'sin-precio', 'price_anual' => null]);

        $this->post(route('cart.store', $draft))->assertSessionHas('error');
        $this->post(route('cart.store', $withoutPrice))->assertSessionHas('error');
        $this->post('/carrito/no-existe')->assertNotFound();
        $this->assertSame([], session('cart.course_ids', []));
    }

    public function test_adding_the_same_digital_course_does_not_duplicate_it(): void
    {
        $course = $this->course();

        $this->post(route('cart.store', $course));
        $this->post(route('cart.store', $course));

        $this->assertSame([$course->id], session('cart.course_ids'));
    }

    public function test_quantity_is_fixed_to_one_and_header_shows_server_count(): void
    {
        $course = $this->course();
        $this->withSession(['cart.course_ids' => [$course->id]])
            ->patch(route('cart.update', $course), ['quantity' => 1])
            ->assertSessionHasNoErrors();

        $this->patch(route('cart.update', $course), ['quantity' => 2])
            ->assertSessionHasErrors('quantity');

        $this->get(route('home'))->assertOk()->assertSee('Carrito')->assertSee('1');
        $this->assertSame([$course->id], session('cart.course_ids'));
    }

    public function test_it_removes_and_clears_courses(): void
    {
        $first = $this->course(['slug' => 'primero']);
        $second = $this->course(['slug' => 'segundo']);
        $this->withSession(['cart.course_ids' => [$first->id, $second->id]])
            ->delete(route('cart.destroy', $first));

        $this->assertSame([$second->id], session('cart.course_ids'));
        $this->delete(route('cart.clear'));
        $this->assertSame([], session('cart.course_ids', []));
    }

    public function test_total_is_recalculated_from_current_database_prices(): void
    {
        $first = $this->course(['slug' => 'primero', 'price_anual' => '10.10']);
        $second = $this->course(['slug' => 'segundo', 'price_anual' => '20.20']);

        $response = $this->withSession(['cart.course_ids' => [$first->id, $second->id]])
            ->get(route('cart.index'));

        $response->assertOk()->assertSee('S/ 30.30');
    }

    private function course(array $attributes = []): Course
    {
        $area = Area::firstOrCreate(
            ['slug' => 'area-carrito'],
            ['name' => 'Área carrito', 'sort_order' => 0, 'is_default' => true],
        );

        return Course::create(array_merge([
            'area_id' => $area->id,
            'title' => 'Curso carrito',
            'slug' => 'curso-carrito',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => '10.00',
        ], $attributes));
    }
}
