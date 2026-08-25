<?php

namespace Tests\Unit\Support;

use App\Support\VideoEmbed;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    #[DataProvider('youtubeUrlsProvider')]
    public function test_provider_detects_youtube(string $url): void
    {
        $this->assertSame('youtube', VideoEmbed::provider($url));
    }

    public static function youtubeUrlsProvider(): array
    {
        return [
            'standard' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            'without www' => ['https://youtube.com/watch?v=dQw4w9WgXcQ'],
            'short url' => ['https://youtu.be/dQw4w9WgXcQ'],
            'http' => ['http://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ];
    }

    #[DataProvider('vimeoUrlsProvider')]
    public function test_provider_detects_vimeo(string $url): void
    {
        $this->assertSame('vimeo', VideoEmbed::provider($url));
    }

    public static function vimeoUrlsProvider(): array
    {
        return [
            'standard' => ['https://vimeo.com/123456789'],
            'with www' => ['https://www.vimeo.com/123456789'],
            'http' => ['http://vimeo.com/123456789'],
        ];
    }

    #[DataProvider('invalidUrlsProvider')]
    public function test_provider_returns_null_for_invalid_urls(string $url): void
    {
        $this->assertNull(VideoEmbed::provider($url));
    }

    public static function invalidUrlsProvider(): array
    {
        return [
            'dailymotion' => ['https://www.dailymotion.com/video/x123456'],
            'direct mp4' => ['https://example.com/video.mp4'],
            'random site' => ['https://example.com/watch?v=123'],
            'not a url' => ['not-a-url'],
            'empty' => [''],
        ];
    }

    public function test_id_extracts_youtube_id_from_standard_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', VideoEmbed::id('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
    }

    public function test_id_extracts_youtube_id_from_short_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', VideoEmbed::id('https://youtu.be/dQw4w9WgXcQ'));
    }

    public function test_id_extracts_vimeo_id(): void
    {
        $this->assertSame('123456789', VideoEmbed::id('https://vimeo.com/123456789'));
    }

    public function test_id_returns_null_for_invalid_url(): void
    {
        $this->assertNull(VideoEmbed::id('https://example.com/video'));
    }

    public function test_thumbnail_url_for_youtube(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertSame('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', VideoEmbed::thumbnailUrl($url));
    }

    public function test_thumbnail_url_returns_null_for_invalid_url(): void
    {
        $this->assertNull(VideoEmbed::thumbnailUrl('https://example.com/video'));
    }

    public function test_embed_url_for_youtube_uses_nocookie_domain(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', VideoEmbed::embedUrl($url));
    }

    public function test_embed_url_for_youtube_short_url(): void
    {
        $url = 'https://youtu.be/dQw4w9WgXcQ';
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', VideoEmbed::embedUrl($url));
    }

    public function test_embed_url_for_vimeo(): void
    {
        $url = 'https://vimeo.com/123456789';
        $this->assertSame('https://player.vimeo.com/video/123456789', VideoEmbed::embedUrl($url));
    }

    public function test_embed_url_returns_null_for_invalid_url(): void
    {
        $this->assertNull(VideoEmbed::embedUrl('https://example.com/video'));
    }
}
