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
}
