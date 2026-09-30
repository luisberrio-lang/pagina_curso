<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEntrypointTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_from_admin_entrypoint_to_login_with_intended_destination(): void
    {
        $this->get(route('admin.entry'))
            ->assertRedirect(route('login'));

        $this->assertSame(route('admin.entry'), session('url.intended'));
    }

    public function test_admin_entrypoint_redirects_authenticated_admin_to_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.entry'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('href="'.route('admin.dashboard').'"', false);
    }

    public function test_admin_login_preserves_intended_admin_flow(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->get(route('admin.entry'))->assertRedirect(route('login'));

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.entry'));

        $this->followingRedirects()
            ->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Dashboard Administrador');
    }

    public function test_normal_user_is_denied_from_entrypoint_and_every_admin_module(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        foreach ([
            route('admin.entry'),
            route('admin.dashboard'),
            route('admin.courses.index'),
            route('admin.areas.index'),
            route('admin.orders.index'),
            route('admin.payments.index'),
            route('admin.configuration.show'),
        ] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('href="'.route('admin.dashboard').'"', false)
            ->assertDontSee('data-admin-logout', false);
    }

    public function test_admin_controls_persist_sitewide_until_logout(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $area = Area::create([
            'name' => 'Área navegación',
            'slug' => 'area-navegacion',
            'sort_order' => 0,
            'is_default' => true,
        ]);
        $course = Course::create([
            'area_id' => $area->id,
            'title' => 'Curso navegación',
            'slug' => 'curso-navegacion',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => '49.90',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Dashboard', 'Inicio', 'Programas/Cursos', 'Precios', 'FAQ', 'Carrito'])
            ->assertSee('Cerrar sesión')
            ->assertSee('method="POST" action="'.route('logout').'" data-admin-logout', false);

        $this->assertGreaterThan(
            strpos($response->getContent(), '</nav>'),
            strpos($response->getContent(), 'data-admin-logout'),
        );
        $this->assertSame(2, substr_count($response->getContent(), 'data-admin-logout'));
        $response->assertSee('class="btn btn-accent btn-accent-soft w-full', false);

        foreach ([
            route('home'),
            route('courses.index'),
            route('courses.show', $course),
            route('price'),
            route('faq'),
            route('cart.index'),
            route('legal.terms'),
            route('contact'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('href="'.route('admin.dashboard').'"', false)
                ->assertSee('method="POST" action="'.route('logout').'" data-admin-logout', false);
            $this->assertAuthenticatedAs($admin);
        }

        $this->get(route('admin.entry'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->withSession(['admin_session_marker' => 'active'])
            ->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('admin_session_marker');

        $this->assertGuest();
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('href="'.route('admin.dashboard').'"', false)
            ->assertDontSee('data-admin-logout', false);
        $this->get(route('admin.entry'))->assertRedirect(route('login'));
    }

    public function test_public_header_contains_only_commercial_navigation(): void
    {
        $html = view('partials.site-header', ['cartCount' => 0])->render();

        $this->assertStringNotContainsString('href="'.route('login').'"', $html);
        $this->assertStringNotContainsString('href="'.route('register').'"', $html);
        $this->assertStringNotContainsString('href="'.route('admin.entry').'"', $html);
        $this->assertStringNotContainsString('href="'.route('admin.dashboard').'"', $html);
        $this->assertStringNotContainsString('href="'.route('profile.edit').'"', $html);
        $this->assertStringNotContainsString(route('logout'), $html);
        $this->assertStringNotContainsString('Cerrar sesión', $html);
        $this->assertStringNotContainsString('Iniciar sesión', $html);
        $this->assertStringNotContainsString('Crear cuenta', $html);
        $this->assertStringNotContainsString('Dashboard', $html);
        $this->assertStringContainsString('Inicio', $html);
        $this->assertStringContainsString('Programas/Cursos', $html);
        $this->assertStringContainsString('Carrito', $html);
    }

    public function test_public_home_and_footer_do_not_expose_authentication_or_admin_links(): void
    {
        $response = $this->get(route('home'))->assertOk();

        foreach ([
            route('login'),
            route('register'),
            route('admin.entry'),
            route('admin.dashboard'),
            route('profile.edit'),
        ] as $url) {
            $response->assertDontSee('href="'.$url.'"', false);
        }
        $response->assertDontSee(route('logout'));
        $response->assertDontSee('Cerrar sesión');

        $footer = view('partials.site-footer')->render();
        $this->assertStringNotContainsString(route('login'), $footer);
        $this->assertStringNotContainsString(route('register'), $footer);
        $this->assertStringNotContainsString('/admin', $footer);
    }

    public function test_public_pages_do_not_expose_admin_configuration(): void
    {
        config()->set('admin', [
            'name' => 'Nombre Administrativo Privado',
            'email' => 'admin-privado@example.com',
            'phone' => '+51 900 000 999',
            'password' => 'Clave-Privada-No-Visible',
        ]);

        foreach ([route('home'), route('courses.index'), route('cart.index'), route('faq'), route('contact')] as $url) {
            $response = $this->get($url)->assertOk();
            foreach (config('admin') as $secret) {
                $response->assertDontSee((string) $secret);
            }
        }
    }
}
