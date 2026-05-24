<?php

namespace Tests\Unit\Support;

use App\Support\Html\HtmlSanitizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_keeps_basic_formatting(): void
    {
        $html = '<h2>Title</h2><p>Some <strong>bold</strong> and <em>italic</em> text.</p><ul><li>One</li></ul>';

        $this->assertSame($html, HtmlSanitizer::clean($html));
    }

    public function test_it_keeps_arabic_text_intact(): void
    {
        $html = '<p dir="rtl">مرحبا بكم في المتجر</p>';

        $this->assertSame($html, HtmlSanitizer::clean($html));
    }

    #[DataProvider('maliciousInput')]
    public function test_it_removes_dangerous_markup(string $input, string $forbidden): void
    {
        $this->assertStringNotContainsStringIgnoringCase($forbidden, HtmlSanitizer::clean($input));
    }

    public static function maliciousInput(): array
    {
        return [
            'script tag'          => ['<p>Hi</p><script>alert(1)</script>', '<script'],
            'event handler'       => ['<img src="/a.png" onerror="alert(1)">', 'onerror'],
            'javascript link'     => ['<a href="javascript:alert(1)">x</a>', 'javascript:'],
            'encoded js link'     => ['<a href="jav&#x61;script:alert(1)">x</a>', 'script:'],
            'iframe'              => ['<iframe src="https://evil.test"></iframe>', '<iframe'],
            'inline style'        => ['<p style="position:fixed">x</p>', 'style='],
            'protocol relative'   => ['<img src="//evil.test/x.png">', 'evil.test'],
            'svg payload'         => ['<svg onload="alert(1)"></svg>', '<svg'],
        ];
    }

    public function test_external_links_opened_in_new_tab_get_safe_rel(): void
    {
        $clean = HtmlSanitizer::clean('<a href="https://example.com" target="_blank">x</a>');

        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_unknown_tags_are_unwrapped_but_text_is_kept(): void
    {
        $this->assertSame('<p>keep me</p>', HtmlSanitizer::clean('<p><marquee>keep me</marquee></p>'));
    }

    public function test_empty_input_returns_empty_string(): void
    {
        $this->assertSame('', HtmlSanitizer::clean(null));
        $this->assertSame('', HtmlSanitizer::clean('   '));
    }
}
