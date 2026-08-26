<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    private const BUSINESS_NAME = 'Cursos de Ingeniería Online';

    public function test_business_name_has_a_safe_commercial_fallback(): void
    {
        config()->set('app.name', 'Laravel');

        $this->assertSame(self::BUSINESS_NAME, config('shop.business.name'));
        $this->assertNotSame('Laravel', config('shop.business.name'));
    }

    public function test_public_layout_contact_and_footer_use_commercial_branding(): void
    {
        foreach (['home', 'contact', 'courses.index'] as $route) {
            $response = $this->get(route($route));

            $response->assertOk()->assertSee(self::BUSINESS_NAME);
            $this->assertDoesNotMatchRegularExpression('/laravel/i', $response->getContent());
        }
    }

    public function test_authentication_uses_commercial_branding(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()->assertSee(self::BUSINESS_NAME);
        $this->assertDoesNotMatchRegularExpression('/laravel/i', $response->getContent());
    }

    public function test_legal_pages_use_commercial_branding(): void
    {
        foreach (['legal.terms', 'legal.privacy', 'legal.refunds', 'legal.delivery'] as $route) {
            $response = $this->get(route($route));

            $response->assertOk()->assertSee(self::BUSINESS_NAME);
            $this->assertDoesNotMatchRegularExpression('/laravel/i', $response->getContent());
        }
    }

    public function test_admin_uses_commercial_branding(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()->assertSee(self::BUSINESS_NAME);
        $this->assertDoesNotMatchRegularExpression('/laravel/i', $response->getContent());
    }
}
