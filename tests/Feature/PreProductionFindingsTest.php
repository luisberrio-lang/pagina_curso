<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\CourseImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PreProductionFindingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_lists_share_the_commercial_course_filter(): void
    {
        $area = $this->area();
        $valid = $this->course($area, ['title' => 'Curso válido', 'slug' => 'curso-valido', 'is_featured' => true]);
        $invalid = $this->course($area, ['title' => 'Curso sin precio', 'slug' => 'curso-sin-precio', 'price_anual' => null, 'is_featured' => true]);

        foreach ([route('home'), route('price'), route('courses.byArea', $area)] as $url) {
            $this->get($url)->assertOk()->assertSee($valid->title)->assertDontSee($invalid->title);
        }

        $this->get(route('courses.show', $invalid))->assertNotFound();
    }

    public function test_automatic_course_and_area_slugs_are_unique(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $area = $this->area();

        foreach ([1, 2] as $attempt) {
            $this->actingAs($admin)->post(route('admin.courses.store'), [
                'area_id' => $area->id,
                'title' => 'Curso repetido',
                'price_anual' => '10.00',
            ])->assertSessionHasNoErrors();
        }

        $this->assertDatabaseHas('courses', ['slug' => 'curso-repetido']);
        $this->assertDatabaseHas('courses', ['slug' => 'curso-repetido-2']);

        foreach ([1, 2] as $attempt) {
            $this->actingAs($admin)->post(route('admin.areas.store'), ['name' => 'Área repetida'])
                ->assertSessionHasNoErrors();
        }

        $this->assertDatabaseHas('areas', ['slug' => 'area-repetida']);
        $this->assertDatabaseHas('areas', ['slug' => 'area-repetida-2']);
    }

    public function test_area_with_courses_is_protected_and_empty_area_can_be_deleted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $occupied = $this->area();
        $course = $this->course($occupied);
        $empty = Area::create(['name' => 'Vacía', 'slug' => 'vacia']);

        $this->actingAs($admin)->delete(route('admin.areas.destroy', $occupied))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('areas', ['id' => $occupied->id]);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);

        $this->actingAs($admin)->delete(route('admin.areas.destroy', $empty))
            ->assertSessionHas('ok');
        $this->assertDatabaseMissing('areas', ['id' => $empty->id]);
    }

    public function test_admin_editors_sanitize_stored_html(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $course = $this->course($this->area(), [
            'description' => '<p onclick="alert(1)">Contenido</p><script>alert(2)</script>',
            'syllabus' => '<div onmouseover="alert(3)"><strong>Temario</strong></div>',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.courses.edit', $course));

        $response->assertOk()
            ->assertSee('<p>Contenido</p>', false)
            ->assertDontSee('<script>alert(2)</script>', false)
            ->assertDontSee('onclick=', false)
            ->assertDontSee('onmouseover=', false);
    }

    public function test_cover_replacement_and_course_deletion_keep_storage_consistent(): void
    {
        $this->assertSame(public_path('storage'), config('filesystems.disks.public.root'));
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $course = $this->course($this->area(), ['cover_path' => 'courses/covers/old.webp']);
        Storage::disk('public')->put($course->cover_path, 'old');
        Storage::disk('public')->put('courses/samples/sample.jpg', 'sample');
        CourseImage::create(['course_id' => $course->id, 'path' => 'courses/samples/sample.jpg']);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'area_id' => $course->area_id,
            'title' => $course->title,
            'slug' => $course->slug,
            'price_anual' => '20.00',
            'cover' => UploadedFile::fake()->image('new.jpg', 900, 500),
        ])->assertSessionHasNoErrors();

        $course->refresh();
        Storage::disk('public')->assertMissing('courses/covers/old.webp');
        Storage::disk('public')->assertExists($course->cover_path);

        $newCover = $course->cover_path;
        $this->actingAs($admin)->delete(route('admin.courses.destroy', $course))->assertSessionHas('ok');
        Storage::disk('public')->assertMissing($newCover);
        Storage::disk('public')->assertMissing('courses/samples/sample.jpg');
    }

    public function test_commercial_information_is_truthful_and_authentication_is_in_spanish(): void
    {
        config()->set('shop.business.email', null);
        config()->set('shop.business.country_name', 'Perú');
        config()->set('shop.business.support_hours', null);

        $this->get(route('contact'))->assertOk()->assertSee('Perú')->assertDontSee('mailto:', false);
        $this->get(route('home'))->assertOk()->assertDontSee('24/7')->assertSee('mediante nuestros canales de contacto');
        $this->get(route('login'))->assertOk()->assertSee('Correo electrónico')->assertSee('Contraseña')->assertDontSee('Remember me');
        $this->get(route('password.request'))->assertOk()->assertSee('Recuperar contraseña')->assertDontSee('Forgot your password');
    }

    private function area(): Area
    {
        return Area::create(['name' => 'Ingeniería', 'slug' => 'ingenieria', 'is_default' => true]);
    }

    private function course(Area $area, array $attributes = []): Course
    {
        return Course::create(array_merge([
            'area_id' => $area->id,
            'title' => 'Curso comercial',
            'slug' => 'curso-comercial',
            'is_published' => true,
            'is_featured' => false,
            'price_anual' => '10.00',
        ], $attributes));
    }
}
