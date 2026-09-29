<?php

namespace Tests\Feature;

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
            ->assertOk();
    }

    public function test_admin_login_preserves_intended_admin_flow(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->get(route('admin.entry'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.entry'));

        $this->get(route('admin.entry'))
            ->assertRedirect(route('admin.dashboard'));
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
    }

    public function test_public_header_contains_only_commercial_navigation(): void
    {
        $html = view('partials.site-header', ['cartCount' => 0])->render();

        $this->assertStringNotContainsString('href="'.route('login').'"', $html);
        $this->assertStringNotContainsString('href="'.route('register').'"', $html);
        $this->assertStringNotContainsString('href="'.route('admin.entry').'"', $html);
        $this->assertStringNotContainsString('href="'.route('admin.dashboard').'"', $html);
        $this->assertStringNotContainsString('href="'.route('profile.edit').'"', $html);
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
