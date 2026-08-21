<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new HtmlSanitizer;
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function cleanProvider(): array
    {
        return [
            'empty input' => ['', ''],
            'null input' => [null, ''],
            'plain text' => ['hello world', 'hello world'],

            'allowed tags preserved' => [
                '<p>Hello <strong>world</strong></p>',
                '<p>Hello <strong>world</strong></p>',
            ],

            'headings preserved' => [
                '<h2>Title</h2><h3>Sub</h3>',
                '<h2>Title</h2><h3>Sub</h3>',
            ],

            'lists preserved' => [
                '<ul><li>a</li><li>b</li></ul>',
                '<ul><li>a</li><li>b</li></ul>',
            ],

            'script tag is removed' => [
                '<p>before</p><script>alert(1)</script><p>after</p>',
                '<p>before</p>alert(1)<p>after</p>',
            ],

            'iframe from unknown host is removed' => [
                '<iframe src="https://evil.com/embed"></iframe><p>safe</p>',
                '<p>safe</p>',
            ],

            'onclick attribute stripped' => [
                '<p onclick="alert(1)">click</p>',
                '<p>click</p>',
            ],

            'onerror attribute stripped' => [
                '<p><img src="/x.webp" onerror="alert(1)" /></p>',
                '<p><img src="/x.webp"></p>',
            ],

            'javascript: href stripped' => [
                '<a href="javascript:alert(1)">click</a>',
                'click',
            ],

            'data: href stripped' => [
                '<a href="data:text/html,<script>alert(1)</script>">x</a>',
                'x',
            ],

            'https href preserved' => [
                '<a href="https://example.com">good</a>',
                '<a href="https://example.com">good</a>',
            ],

            'relative href preserved' => [
                '<a href="/posts/hello">good</a>',
                '<a href="/posts/hello">good</a>',
            ],

            'mailto preserved' => [
                '<a href="mailto:hi@example.com">good</a>',
                '<a href="mailto:hi@example.com">good</a>',
            ],

            'style attribute stripped' => [
                '<p style="color:red">x</p>',
                '<p>x</p>',
            ],

            'class attribute stripped' => [
                '<p class="big">x</p>',
                '<p>x</p>',
            ],

            'unknown attribute on a stripped' => [
                '<a href="/x" target="_blank" rel="noopener">x</a>',
                '<a href="/x">x</a>',
            ],

            'unknown tag stripped, text preserved' => [
                '<marquee>rolling</marquee>',
                'rolling',
            ],

            'form tag stripped' => [
                '<form action="/x"><input type="text" /></form>',
                '',
            ],

            'inline code preserved' => [
                '<p>Use <code>EPP</code> always.</p>',
                '<p>Use <code>EPP</code> always.</p>',
            ],

            'table preserved' => [
                '<table><thead><tr><th>Header</th></tr></thead><tbody><tr><td>Cell</td></tr></tbody></table>',
                '<table><thead><tr><th>Header</th></tr></thead><tbody><tr><td>Cell</td></tr></tbody></table>',
            ],

            'youtube iframe preserved with wrapper' => [
                '<div data-youtube-video><iframe src="https://www.youtube.com/embed/abc123" width="640" height="480" frameborder="0" allowfullscreen></iframe></div>',
                '<div data-youtube-video><iframe src="https://www.youtube.com/embed/abc123" width="640" height="480" frameborder="0" allowfullscreen></iframe></div>',
            ],

            'youtube-nocookie iframe preserved' => [
                '<div data-youtube-video><iframe src="https://www.youtube-nocookie.com/embed/abc123" width="640" height="480" frameborder="0" allowfullscreen></iframe></div>',
                '<div data-youtube-video><iframe src="https://www.youtube-nocookie.com/embed/abc123" width="640" height="480" frameborder="0" allowfullscreen></iframe></div>',
            ],

            'vimeo iframe preserved' => [
                '<div data-youtube-video><iframe src="https://player.vimeo.com/video/123456" width="640" height="480" frameborder="0" allowfullscreen></iframe></div>',
                '<div data-youtube-video><iframe src="https://player.vimeo.com/video/123456" width="640" height="480" frameborder="0" allowfullscreen></iframe></div>',
            ],

            'arbitrary div unwrapped' => [
                '<div class="article"><p>content</p></div>',
                '<p>content</p>',
            ],

            'img width and height preserved' => [
                '<img src="/x.webp" alt="foo" width="200" height="150">',
                '<img src="/x.webp" alt="foo" width="200" height="150">',
            ],

            'img data-align center preserved' => [
                '<img src="/x.webp" alt="foo" data-align="center">',
                '<img src="/x.webp" alt="foo" data-align="center">',
            ],

            'img data-align left preserved' => [
                '<img src="/x.webp" alt="foo" data-align="left">',
                '<img src="/x.webp" alt="foo" data-align="left">',
            ],

            'img data-align right preserved' => [
                '<img src="/x.webp" alt="foo" data-align="right">',
                '<img src="/x.webp" alt="foo" data-align="right">',
            ],

            'img data-align invalid value stripped' => [
                '<img src="/x.webp" alt="foo" data-align="top">',
                '<img src="/x.webp" alt="foo">',
            ],

            'img data-align with script stripped' => [
                '<img src="/x.webp" alt="foo" data-align="javascript:alert(1)">',
                '<img src="/x.webp" alt="foo">',
            ],

            'p data-clear-float preserved' => [
                '<p data-clear-float="true">Below the image</p>',
                '<p data-clear-float="true">Below the image</p>',
            ],

            'javascript: in iframe src stripped' => [
                '<iframe src="javascript:alert(1)"></iframe><p>safe</p>',
                '<p>safe</p>',
            ],
        ];
    }

    #[DataProvider('cleanProvider')]
    public function test_clean(?string $input, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->clean($input));
    }

    public function test_complex_pasted_content_is_sanitized(): void
    {
        $dirty = '<div class="article" style="color:red" onclick="evil()"><h1>Title</h1><p>Body with <a href="javascript:bad">link</a> and <strong>strong</strong>.</p></div>';
        $clean = $this->sanitizer->clean($dirty);

        // Unwrapped tags should not appear.
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringNotContainsString('class="article"', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<div', $clean);
        $this->assertStringNotContainsString('</div>', $clean);

        // Inner allowed content preserved. <h1> is unwrapped (not whitelisted),
        // but its text remains.
        $this->assertStringContainsString('Title', $clean);
        $this->assertStringContainsString('<strong>strong</strong>', $clean);
    }

    public function test_youtube_embed_survives_round_trip(): void
    {
        $html = '<p>Watch this:</p><div data-youtube-video><iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" width="640" height="480" frameborder="0" allowfullscreen></iframe></div><p>End.</p>';
        $clean = $this->sanitizer->clean($html);

        $this->assertStringContainsString('data-youtube-video', $clean);
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $clean);
        $this->assertStringContainsString('<p>Watch this:</p>', $clean);
        $this->assertStringContainsString('<p>End.</p>', $clean);
    }

    public function test_non_youtube_iframe_is_removed(): void
    {
        $html = '<iframe src="https://evil.com/track" width="640" height="480"></iframe><p>safe</p>';
        $clean = $this->sanitizer->clean($html);

        $this->assertStringNotContainsString('evil.com', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringContainsString('<p>safe</p>', $clean);
    }
}
