<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_page_gets_an_enforced_content_security_policy(): void
    {
        $csp = $this->get(route('home'))->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
    }

    public function test_the_full_policy_is_reported_without_blocking(): void
    {
        $policy = $this->get(route('home'))->headers->get('Content-Security-Policy-Report-Only');

        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString('https://cdn.moyasar.com', $policy);
    }

    public function test_secure_requests_upgrade_mixed_content(): void
    {
        $csp = $this->get(str_replace('http://', 'https://', route('home')))->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('upgrade-insecure-requests', $csp);
    }
}
