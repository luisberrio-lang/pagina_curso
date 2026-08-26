<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_legal_and_contact_pages_are_public(): void
    {
        foreach (['legal.terms', 'legal.privacy', 'legal.refunds', 'legal.delivery', 'contact'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_footer_links_to_every_legal_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        foreach (['legal.terms', 'legal.privacy', 'legal.refunds', 'legal.delivery', 'contact'] as $route) {
            $response->assertSee(route($route), false);
        }
    }

    public function test_terms_are_professional_complete_and_consistent(): void
    {
        $response = $this->get(route('legal.terms'));

        $response->assertOk()
            ->assertSee('Cursos de Ingeniería Online')
            ->assertSee('Catálogo y precios')
            ->assertSee('Acceso y entrega digital')
            ->assertSee('Uso personal del contenido')
            ->assertSee('Propiedad intelectual y restricciones')
            ->assertSee('revender cursos')
            ->assertSee('redistribuir materiales')
            ->assertSee(route('legal.delivery'), false)
            ->assertSee(route('legal.privacy'), false)
            ->assertSee(route('legal.refunds'), false)
            ->assertSee(route('contact'), false);

        $this->assertDoesNotMatchRegularExpression(
            '/laravel|lorem ipsum|sandbox|texto de ejemplo|placeholder/i',
            $response->getContent()
        );
    }

    public function test_public_delivery_language_does_not_promise_unimplemented_automation(): void
    {
        foreach (['home', 'faq', 'legal.terms', 'legal.delivery'] as $route) {
            $response = $this->get(route($route));

            $response->assertOk();
            $this->assertDoesNotMatchRegularExpression(
                '/acceso inmediato|tiempo estimado:\s*5 minutos|entrega automática/i',
                $response->getContent()
            );
        }
    }
}
