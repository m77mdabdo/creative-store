<?php

namespace Tests\Feature\Storefront;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_the_sitemap_lists_active_products_in_both_languages(): void
    {
        $product = $this->product(100, ['slug' => 'linen-shirt']);
        $this->product(100, ['slug' => 'hidden-draft', 'status' => 'draft']);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('/ar/products/linen-shirt', $xml);
        $this->assertStringContainsString('/en/products/linen-shirt', $xml);
        $this->assertStringNotContainsString('hidden-draft', $xml);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /', false);
    }

    public function test_responses_carry_security_headers(): void
    {
        $this->get('/robots.txt')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
