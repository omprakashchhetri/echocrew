<?php

use App\Libraries\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $s;

    protected function setUp(): void
    {
        $this->s = new HtmlSanitizer();
    }

    public function testKeepsAllowedMarkup(): void
    {
        $this->assertSame('<h2>T</h2><p>Hi <strong>there</strong></p>', $this->s->clean('<h2>T</h2><p>Hi <strong>there</strong></p>'));
    }

    public function testRemovesScriptsAndEventHandlers(): void
    {
        $out = $this->s->clean('<p onclick="x()">a</p><script>alert(1)</script><img src="x.png" onerror="y()">');
        $this->assertStringNotContainsString('script', $out);
        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringNotContainsString('onerror', $out);
    }

    public function testBlocksDangerousUrlSchemes(): void
    {
        $out = $this->s->clean('<a href="javascript:alert(1)">x</a><a href=" JaVa&#x09;script:alert(1)">y</a><a href="data:text/html,hi">z</a>');
        $this->assertStringNotContainsString('href', $out);
    }

    public function testKeepsSafeLinksAndAddsRel(): void
    {
        $out = $this->s->clean('<a href="https://example.com">x</a><a href="/services">y</a>');
        $this->assertStringContainsString('href="https://example.com" rel="noopener"', $out);
        $this->assertStringContainsString('href="/services"', $out);
    }

    public function testDemotesH1AndUnwrapsUnknownTags(): void
    {
        $this->assertSame('<h2>A</h2><p>b</p>', $this->s->clean('<h1>A</h1><font><p>b</p></font>'));
    }

    public function testEmptyInput(): void
    {
        $this->assertSame('', $this->s->clean('   '));
    }
}
