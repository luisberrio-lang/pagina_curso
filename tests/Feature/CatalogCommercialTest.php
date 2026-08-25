<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogCommercialTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_shows_published_courses_and_hides_drafts(): void
    {
        $area = $this->area();
        $published = $this->course($area, ['title' => 'Curso publicado', 'slug' => 'curso-publicado']);
        $draft = $this->course($area, [
            'title' => 'Curso borrador',
            'slug' => 'curso-borrador',
            'is_published' => false,
        ]);

        $response = $this->get(route('courses.byArea', $area));

        $response->assertOk();
        $response->assertSee($published->title);
        $response->assertDontSee($draft->title);
        $this->get(route('courses.show', $draft))->assertNotFound();
    }

    public function test_course_detail_uses_the_official_backend_price_and_pen_currency(): void
    {
        config()->set('shop.currency', 'PEN');
        $course = $this->course($this->area(), [
            'price_anual' => '49.90',
            'price_previous' => '79.90',
        ]);

        $response = $this->get(route('courses.show', $course));

        $response->assertOk();
        $response->assertSee('S/ 49.90');
        $response->assertSee('S/ 79.90');
        $response->assertDontSee('name="currency"', false);
        $this->assertSame('49.90', $course->currentPrice());
        $this->assertSame('PEN', $course->commercialData()['currency']);
    }

    public function test_admin_can_store_a_valid_price_and_previous_price_is_optional(): void
    {
        $area = $this->area();
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
            'area_id' => $area->id,
            'title' => 'Curso con precio',
            'price_anual' => '25.50',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('courses', [
            'title' => 'Curso con precio',
            'price_anual' => 25.50,
            'price_previous' => null,
        ]);
    }

    public function test_admin_cannot_store_negative_or_zero_prices(): void
    {
        $area = $this->area();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'area_id' => $area->id,
            'title' => 'Precio negativo',
            'price_anual' => '-1.00',
        ])->assertSessionHasErrors('price_anual');

        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'area_id' => $area->id,
            'title' => 'Precio cero',
            'price_anual' => '0.00',
        ])->assertSessionHasErrors('price_anual');
    }

    public function test_admin_cannot_store_a_negative_previous_price(): void
    {
        $area = $this->area();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'area_id' => $area->id,
            'title' => 'Anterior negativo',
            'price_anual' => '20.00',
            'price_previous' => '-10.00',
        ])->assertSessionHasErrors('price_previous');
    }

    private function area(): Area
    {
        return Area::create([
            'name' => 'Área de pruebas',
            'slug' => 'area-pruebas',
            'sort_order' => 0,
            'is_default' => true,
        ]);
    }

    private function course(Area $area, array $attributes = []): Course
    {
        return Course::create(array_merge([
            'area_id' => $area->id,
            'title' => 'Curso comercial',
            'slug' => 'curso-comercial',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => '49.90',
        ], $attributes));
    }
}
