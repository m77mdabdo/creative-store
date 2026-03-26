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

    public function test_scripts_need_the_nonce_of_this_request(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertMatchesRegularExpression("/script-src 'nonce-([A-Za-z0-9]+)' 'strict-dynamic'/", $csp);
        preg_match("/'nonce-([A-Za-z0-9]+)'/", $csp, $match);

        $this->assertStringContainsString('nonce="' . $match[1] . '"', $response->getContent());
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertNull($response->headers->get('Content-Security-Policy-Report-Only'));

        $next = $this->get(route('home'))->headers->get('Content-Security-Policy');
        $this->assertStringNotContainsString($match[1], $next);
    }

    public function test_no_view_uses_inline_event_handlers_or_scripts_without_a_nonce(): void
    {
        $offenders = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $html = file_get_contents($file->getPathname());

            if (preg_match('/\son(click|submit|change|input|load|error|key\w+|mouse\w+|focus|blur)\s*=/i', $html)
                || preg_match('/<script(?![^>]*\bnonce=)[\s>]/i', $html)
                || str_contains($html, 'javascript:')) {
                $offenders[] = str_replace(resource_path('views') . '/', '', $file->getPathname());
            }
        }

        $this->assertSame([], $offenders, 'These views would be blocked by the Content-Security-Policy.');
    }

    public function test_secure_requests_upgrade_mixed_content(): void
    {
        $csp = $this->get(str_replace('http://', 'https://', route('home')))->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('upgrade-insecure-requests', $csp);
    }
}
