<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ForceHttpsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // TrustHosts stores the allowed hosts statically on the Request class;
        // clear them so later tests on http://localhost are not rejected.
        Request::setTrustedHosts([]);

        parent::tearDown();
    }

    public function test_plain_http_is_redirected_to_https_in_production(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://store.test']);

        $this->get('http://store.test/en/products?page=2')
            ->assertStatus(301)
            ->assertRedirect('https://store.test/en/products?page=2');
    }

    public function test_https_requests_and_the_health_check_pass_through(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://store.test']);

        $this->get('http://store.test/up')->assertOk();
        $this->get('https://store.test/up')->assertOk();
    }

    public function test_local_development_over_http_is_left_alone(): void
    {
        config(['app.url' => 'http://localhost']);

        $this->get('/up')->assertOk();
    }
}
